<?php namespace Todaymade\Daux\Format\Confluence;

use Symfony\Component\Console\Output\OutputInterface;
use Todaymade\Daux\Config;
use Todaymade\Daux\Daux;
use Todaymade\Daux\Format\Base\RunAction;
use Todaymade\Daux\Tree\Content;
use Todaymade\Daux\Tree\Directory;
use Todaymade\Daux\Tree\Entry;

class Generator
{
    use RunAction;

    /**
     * @var string
     */
    protected $prefix;

    public function generate(Daux $daux, OutputInterface $output, $width)
    {
        $confluence = $daux->getParams()['confluence'];

        $this->prefix = trim($confluence['prefix']) . " ";

        $params = $daux->getParams();

        $tree = $this->runAction(
            "Generating Tree ...",
            $output,
            $width,
            function() use ($daux, $params) {
                $tree = $this->generateRecursive($daux->tree, $params);
                $tree['title'] = $this->prefix . $daux->getParams()['title'];

                return $tree;
            }
        );

        $output->writeln("Start Publishing...");

        $publisher = new Publisher($confluence);
        $publisher->output = $output;
        $publisher->width = $width;
        $publisher->publish($tree);
    }

    private function generateRecursive(Entry $tree, Config $params, $base_url = '')
    {
        $final = ['title' => $this->prefix . $tree->getTitle()];
        $params['base_url'] = $params['base_page'] = $base_url;

        $params['image'] = str_replace('<base_url>', $base_url, $params['image']);
        if ($base_url !== '') {
            $params['entry_page'] = $tree->getFirstPage();
        }
        foreach ($tree->value as $key => $node) {
            if ($node instanceof Directory) {
                $final['children'][$this->prefix . $node->getTitle()] = $this->generateRecursive(
                    $node,
                    $params,
                    '../' . $base_url
                );
            } elseif ($node instanceof Content) {
                $params['request'] = $node->getUrl();
                $params['file_uri'] = $node->getName();

                $data = [
                    'title' => $this->prefix . $node->getTitle(),
                    'file' => $node,
                    'page' => MarkdownPage::fromFile($node, $params),
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
