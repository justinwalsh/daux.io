<?php namespace Todaymade\Daux\Format\Confluence;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ParseException;

class Publisher
{

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

    public function __construct($confluence)
    {
        $this->confluence = $confluence;

        $this->client = new Api($confluence['base_url'], $confluence['user'], $confluence['pass']);
        $this->client->setSpace($confluence['space_id']);
    }

    public function publish(array $tree)
    {
        echo "Getting already published pages...\n";
        $all_published = $this->client->getHierarchy($this->confluence['ancestor_id']);

        echo "Finding Root Page...\n";
        $published = [];
        foreach ($all_published as $page) {
            if ($page['title'] == $tree['title']) {
                $published = $page;
                break;
            }
        }

        echo "Create placeholder pages...\n";
        $published = $this->createRecursive($this->confluence['ancestor_id'], $tree, $published);

        echo "Publishing updates...\n";
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
        if ($this->previous_title != "Creating") {
            $this->previous_title = "Creating";
            echo "Creating Pages...\n";
        }

        echo "- " . $this->niceTitle($entry['file']->getUrl());
        $published['version'] = 1;
        $published['id'] = $this->client->createPage($parent_id, $entry['title'], "The content will come very soon !");
        echo " √ \n";

        return $published;
    }

    protected function createPlaceholderPage($parent_id, $entry, $published)
    {
        if ($this->previous_title != "Creating Placeholder") {
            $this->previous_title = "Creating Placeholder";
            echo "Creating Placeholder Pages...\n";
        }

        echo "- " . $entry['title'];
        $published['version'] = 1;
        $published['id'] = $this->client->createPage($parent_id, $entry['title'], "");
        echo " √ \n";

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
        $callback = function ($parent_id, $entry, $published) {

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
        $callback = function ($parent_id, $entry, $published) {
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

        $trimmed_local = trim($local);
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

        return true;
    }

    protected function updatePage($parent_id, $entry, $published)
    {
        if ($this->previous_title != "Updating") {
            $this->previous_title = "Updating";
            echo "Updating Pages...\n";
        }

        echo "- " . $this->niceTitle($entry['file']->getUrl());

        try {
            if ($this->shouldUpdate($entry['page']->getContent(), $published)) {
                $this->client->updatePage(
                    $parent_id,
                    $published['id'],
                    $published['version'] + 1,
                    $entry['title'],
                    $entry['page']->getContent()
                );
                echo " √\n";
            } else {
                echo " √ (No update needed)\n";
            }

            if (count($entry['page']->attachments)) {
                foreach ($entry['page']->attachments as $attachment) {
                    echo "  With attachment: $attachment[filename]";
                    $this->client->uploadAttachment($published['id'], $attachment);
                    echo " √\n";
                }
            }

        } catch (BadResponseException $e) {
            echo " X Failed with message: " . $e->getMessage() . "\n";
        }
    }
}
