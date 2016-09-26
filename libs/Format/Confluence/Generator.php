<?php namespace Todaymade\Daux\Format\Confluence;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Todaymade\Daux\Config;
use Todaymade\Daux\Console\RunAction;
use Todaymade\Daux\Daux;
use Todaymade\Daux\Tree\Content;
use Todaymade\Daux\Tree\Directory;

class Generator implements \Todaymade\Daux\Format\Base\Generator
{
    use RunAction;

    /** @var string */
    protected $prefix;

    /** @var Daux */
    protected $daux;

    /**
     * @param Daux $daux
     */
    public function __construct(Daux $daux)
    {
        $this->daux = $daux;

        $this->checkConfiguration();
    }

    public function checkConfiguration()
    {
        $config = $this->daux->getParams();
        $confluence = $config->getConfluenceConfiguration();

        if ($confluence == null) {
            throw new \RuntimeException('You must specify your Confluence configuration');
        }

        $mandatory = ['space_id', 'base_url', 'user', 'pass', 'prefix'];
        $errors = [];
        foreach ($mandatory as $key) {
            if (!array_key_exists($key, $confluence)) {
                $errors[] = $key;
            }
        }

        if (count($errors)) {
            throw new \RuntimeException("The following options are mandatory for confluence : '" . implode("', '", $errors) . "'");
        }

        if (!array_key_exists('ancestor_id', $confluence) && !array_key_exists('root_id', $confluence)) {
            throw new \RuntimeException("You must specify an 'ancestor_id' or a 'root_id' for confluence.");
        }
    }

    /**
     * @return array
     */
    public function getContentTypes()
    {
        return [
            new ContentTypes\Markdown\ContentType($this->daux->getParams()),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function generateAll(InputInterface $input, OutputInterface $output, $width)
    {
        $params = $this->daux->getParams();

        $confluence = $params['confluence'];
        $this->prefix = trim($confluence['prefix']) . ' ';

        $tree = $this->runAction(
            'Generating Tree ...',
            $output,
            $width,
            function () use ($params) {
                $tree = $this->generateRecursive($this->daux->tree, $params);
                $tree['title'] = $this->prefix . $params['title'];

                return $tree;
            }
        );

        $output->writeln('Start Publishing...');

        $publisher = new Publisher($confluence);
        $publisher->output = $output;
        $publisher->width = $width;
        $publisher->publish($tree);
    }

    private function generateRecursive(Directory $tree, Config $params, $base_url = '')
    {
        $final = ['title' => $this->prefix . $tree->getTitle()];
        $params['base_url'] = $params['base_page'] = $base_url;

        $params['image'] = str_replace('<base_url>', $base_url, $params['image']);
        if ($base_url !== '') {
            $params['entry_page'] = $tree->getFirstPage();
        }
        foreach ($tree->getEntries() as $key => $node) {
            if ($node instanceof Directory) {
                $final['children'][$this->prefix . $node->getTitle()] = $this->generateRecursive(
                    $node,
                    $params,
                    '../' . $base_url
                );
            } elseif ($node instanceof Content) {
                $params['request'] = $node->getUrl();

                $contentType = $this->daux->getContentTypeHandler()->getType($node);

                $data = [
                    'title' => $this->prefix . $node->getTitle(),
                    'file' => $node,
                    'page' => ContentPage::fromFile($node, $params, $contentType),
                ];

                // As the page is lazily generated
                // We do it now to fail fast in case of problem
                $data['page']->getContent();

                if ($key == 'index.html') {
                    $final['title'] = $this->prefix . $tree->getTitle();
                    $final['file'] = $node;
                    $final['page'] = $data['page'];
                } else {
                    $final['children'][$data['title']] = $data;
                }
            }
        }

        return $final;
    }
}
