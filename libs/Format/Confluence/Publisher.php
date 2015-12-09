<?php namespace Todaymade\Daux\Format\Confluence;

use GuzzleHttp\Exception\BadResponseException;
use Todaymade\Daux\Console\RunAction;

class Publisher
{
    use RunAction;

    /**
     * @var Api
     */
    protected $client;

    /**
     * @var array
     */
    protected $confluence;

    /**
     * @var string
     */
    protected $previous_title;

    /**
     * @var integer terminal width
     */
    public $width;

    /**
     * @var
     */
    public $output;

    /**
     * @param $confluence
     */
    public function __construct($confluence)
    {
        $this->confluence = $confluence;

        $this->client = new Api($confluence['base_url'], $confluence['user'], $confluence['pass']);
        $this->client->setSpace($confluence['space_id']);
    }

    public function run($title, $closure)
    {
        try {
            return $this->runAction($title, $this->output, $this->width, $closure);
        } catch (BadResponseException $e) {
            $this->output->writeLn("    <error>" . $e->getMessage() . "</error>");
        }
    }

    public function publish(array $tree)
    {
        echo "Finding Root Page...\n";
        $pages = $this->client->getList($this->confluence['ancestor_id']);
        $published = null;
        foreach ($pages as $page) {
            if ($page['title'] == $tree['title']) {
                $published = $page;
                break;
            }
        }

        $this->run(
            "Getting already published pages...",
            function() use (&$published) {
                if ($published != null) {
                    $published['children'] = $this->client->getList($published['id'], true);
                }
            }
        );

        $published = $this->run(
            "Create placeholder pages...",
            function() use ($tree, $published) {
                return $this->createRecursive($this->confluence['ancestor_id'], $tree, $published);
            }
        );

        $this->output->writeLn("Publishing updates...");
        $this->updateRecursive($this->confluence['ancestor_id'], $tree, $published);
    }

    protected function niceTitle($title)
    {
        if ($title == "index.html") {
            return "Homepage";
        }

        return rtrim(strtr($title, ['index.html' => '', '.html' => '']), "/");
    }

    protected function createPage($parent_id, $entry, $published)
    {
        echo "- " . $this->niceTitle($entry['file']->getUrl()) . "\n";
        $published['version'] = 1;
        $published['id'] = $this->client->createPage($parent_id, $entry['title'], "The content will come very soon !");

        return $published;
    }

    protected function createPlaceholderPage($parent_id, $entry, $published)
    {
        echo "- " . $entry['title'] . "\n";
        $published['version'] = 1;
        $published['id'] = $this->client->createPage($parent_id, $entry['title'], "");

        return $published;
    }

    protected function recursiveWithCallback($parent_id, $entry, $published, $callback)
    {
        $published = $callback($parent_id, $entry, $published);

        if (array_key_exists('children', $entry)) {
            foreach ($entry['children'] as $child) {
                $pub = [];
                if (isset($published['children']) && array_key_exists($child['title'], $published['children'])) {
                    $pub = $published['children'][$child['title']];
                }

                $published['children'][$child['title']] = $this->recursiveWithCallback(
                    $published['id'],
                    $child,
                    $pub,
                    $callback
                );
            }
        }

        return $published;
    }

    protected function createRecursive($parent_id, $entry, $published)
    {
        $callback = function($parent_id, $entry, $published) {

            //TODO :: remove deleted pages

            // nothing to do if the ID already exists
            if (array_key_exists('id', $published)) {
                return $published;
            }

            if (array_key_exists('page', $entry)) {
                return $this->createPage($parent_id, $entry, $published);
            }

            // If we have no $entry['page'] it means the page
            // doesn't exist, but to respect the hierarchy,
            // we need a blank page there
            return $this->createPlaceholderPage($parent_id, $entry, $published);
        };

        return $this->recursiveWithCallback($parent_id, $entry, $published, $callback);
    }

    protected function updateRecursive($parent_id, $entry, $published)
    {
        $callback = function($parent_id, $entry, $published) {
            if (array_key_exists('id', $published) && array_key_exists('page', $entry)) {
                $this->updatePage($parent_id, $entry, $published);
            }

            return $published;
        };

        return $this->recursiveWithCallback($parent_id, $entry, $published, $callback);
    }

    protected function shouldUpdate($local, $published)
    {
        if (!array_key_exists('content', $published)) {
            return true;
        }

        $trimmed_local = trim($local->getContent());
        $trimmed_distant = trim($published['content']);

        if ($trimmed_local == $trimmed_distant) {
            return false;
        }

        similar_text($trimmed_local, $trimmed_distant, $percent);

        // I consider that if the files are 98% identical you
        // don't need to update. This will work for false positives.
        // But sadly will miss if it's just a typo update
        if ($percent >= 98) {
            return false;
        }

        //DEBUG
        if (getenv("DEBUG") && strtolower(getenv("DEBUG")) != "false") {
            $prefix = 'static/export/';
            if (!is_dir($prefix)) {
                mkdir($prefix, 0777, true);
            }
            $url = $local->getFile()->getUrl();
            file_put_contents($prefix . strtr($url, ['/' => '_', '.html' => '_local.html']), $trimmed_local);
            file_put_contents($prefix . strtr($url, ['/' => '_', '.html' => '_distant.html']), $trimmed_distant);
        }

        return true;
    }

    protected function updatePage($parent_id, $entry, $published)
    {
        if ($this->previous_title != "Updating") {
            $this->previous_title = "Updating";
            echo "Updating Pages...\n";
        }

        $this->run(
            "- " . $this->niceTitle($entry['file']->getUrl()),
            function() use ($entry, $published, $parent_id) {
                if ($this->shouldUpdate($entry['page'], $published)) {
                    $this->client->updatePage(
                        $parent_id,
                        $published['id'],
                        $published['version'] + 1,
                        $entry['title'],
                        $entry['page']->getContent()
                    );
                }
            }
        );

        if (count($entry['page']->attachments)) {
            foreach ($entry['page']->attachments as $attachment) {
                $this->run(
                    "  With attachment: $attachment[filename]",
                    function() use ($published, $attachment) {
                        $this->client->uploadAttachment($published['id'], $attachment);
                    }
                );
            }
        }
    }
}
