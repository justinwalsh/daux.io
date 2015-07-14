<?php namespace Todaymade\Daux\Format\HTML;

use Symfony\Component\Console\Output\OutputInterface;
use Todaymade\Daux\Daux;
use Todaymade\Daux\DauxHelper;
use Todaymade\Daux\Format\Base\RunAction;
use Todaymade\Daux\Generator\Helper;
use Todaymade\Daux\Tree\Directory;
use Todaymade\Daux\Tree\Content;

class Generator
{
    use RunAction;

    public function generate(Daux $daux, $destination, OutputInterface $output, $width)
    {
        $params = $daux->getParams();
        if (is_null($destination)) {
            $destination = $daux->local_base . DS . 'static';
        }

        $this->runAction(
            "Copying Static assets ...",
            $output,
            $width,
            function() use ($destination, $daux) {
                Helper::copyAssets($destination, $daux->local_base);
            }
        );

        $output->writeLn("Generating ...");
        $this->generateRecursive($daux->tree, $destination, $params, $output, $width);
    }

    private function generateRecursive($tree, $output_dir, $params, $output, $width, $base_url = '')
    {
        $params['base_url'] = $params['base_page'] = $base_url;

        // Rebase Theme
        $params['theme'] = DauxHelper::getTheme($params, $base_url);

        $params['image'] = str_replace('<base_url>', $base_url, $params['image']);
        if ($base_url !== '' && empty($params['entry_page'])) {
            $params['entry_page'] = $tree->getFirstPage();
        }
        foreach ($tree->value as $key => $node) {
            if ($node instanceof Directory) {
                $new_output_dir = $output_dir . DS . $key;
                @mkdir($new_output_dir);
                $this->generateRecursive($node, $new_output_dir, $params, $output, $width, '../' . $base_url);
            } elseif ($node instanceof Content) {
                $this->runAction(
                    "- " . $node->getUrl(),
                    $output,
                    $width,
                    function() use ($node, $output_dir, $key, $params) {
                        $params['request'] = $node->getUrl();
                        $params['file_uri'] = $node->getName();

                        $page = MarkdownPage::fromFile($node, $params);
                        file_put_contents($output_dir . DS . $key, $page->getContent());
                    }
                );
            } else {
                $this->runAction(
                    "- " . $node->getUrl(),
                    $output,
                    $width,
                    function() use ($node, $output_dir, $key) {
                        copy($node->getPath(), $output_dir . DS . $key);
                    }
                );
            }
        }
    }
}
