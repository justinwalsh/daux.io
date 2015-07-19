<?php namespace Todaymade\Daux\Format\HTML;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Todaymade\Daux\Config;
use Todaymade\Daux\Daux;
use Todaymade\Daux\DauxHelper;
use Todaymade\Daux\Format\Base\CommonMark\CommonMarkConverter;
use Todaymade\Daux\Format\Base\RunAction;
use Todaymade\Daux\Generator\Helper;
use Todaymade\Daux\Tree\Content;
use Todaymade\Daux\Tree\Directory;
use Todaymade\Daux\Tree\Entry;
use Todaymade\Daux\Tree\Raw;

class Generator implements \Todaymade\Daux\Format\Base\Generator
{
    use RunAction;

    /** @var CommonMarkConverter */
    protected $converter;

    /** @var Daux */
    protected $daux;

    /**
     * @param Daux $daux
     */
    public function __construct(Daux $daux)
    {
        $this->daux = $daux;
        $this->converter = new CommonMarkConverter(['daux' => $this->daux->getParams()]);
    }

    public function generateAll(InputInterface $input, OutputInterface $output, $width)
    {
        $destination = $input->getOption('destination');

        $params = $this->daux->getParams();
        if (is_null($destination)) {
            $destination = $this->daux->local_base . DS . 'static';
        }

        $this->runAction(
            "Copying Static assets ...",
            $output,
            $width,
            function () use ($destination) {
                Helper::copyAssets($destination, $this->daux->local_base);
            }
        );

        $output->writeLn("Generating ...");
        $this->generateRecursive($this->daux->tree, $destination, $params, $output, $width);
    }

    private function rebaseConfig(Config $config, $base_url)
    {
        // Avoid changing the url if it is already correct
        if ($config['base_url'] == $base_url && !empty($config['theme']) && !is_string($config['theme'])) {
            return;
        }

        // Change base url for all links on the pages
        $config['base_url'] = $config['base_page'] = $base_url;
        $config['theme'] = DauxHelper::getTheme($config, $base_url);
        $config['image'] = str_replace('<base_url>', $base_url, $config['image']);
    }

    /**
     * Recursively generate the documentation
     *
     * @param Directory $tree
     * @param string $output_dir
     * @param \Todaymade\Daux\Config $params
     * @param OutputInterface $output
     * @param integer $width
     * @param string $base_url
     * @throws \Exception
     */
    private function generateRecursive(Directory $tree, $output_dir, $params, $output, $width, $base_url = '')
    {
        $this->rebaseConfig($params, $base_url);

        if ($base_url !== '' && empty($params['entry_page'])) {
            $params['entry_page'] = $tree->getFirstPage();
        }

        foreach ($tree->getEntries() as $key => $node) {
            if ($node instanceof Directory) {
                $new_output_dir = $output_dir . DS . $key;
                mkdir($new_output_dir);
                $this->generateRecursive($node, $new_output_dir, $params, $output, $width, '../' . $base_url);

                // Rebase configuration again as $params is a shared object
                $this->rebaseConfig($params, $base_url);
            } else {
                $this->runAction(
                    "- " . $node->getUrl(),
                    $output,
                    $width,
                    function () use ($node, $output_dir, $key, $params) {
                        if (!$node instanceof Content) {
                            copy($node->getPath(), $output_dir . DS . $key);
                            return;
                        }

                        $generated = $this->generateOne($node, $params);
                        file_put_contents($output_dir . DS . $key, $generated->getContent());
                    }
                );
            }
        }
    }

    public function generateOne(Entry $node, $params)
    {
        if ($node instanceof Raw) {
            return new RawPage($node->getPath());
        }

        $params['request'] = $node->getUrl();
        return MarkdownPage::fromFile($node, $params, $this->converter);
    }
}
