<?php namespace Todaymade\Daux\Format\Confluence;

use GuzzleHttp\Exception\ClientException;
use Todaymade\Daux\Daux;
use Todaymade\Daux\Tree\Content;
use Todaymade\Daux\Tree\Directory;
use Todaymade\Daux\Tree\Entry;

class Generator
{
    /**
     * @var Api
     */
    protected $client;

    /**
     * @var string
     */
    protected $prefix;


    public function generate(Daux $daux)
    {
        $confluence = $daux->getParams()['confluence'];

        $this->prefix = trim($confluence['prefix']) . " ";

        $main_title = $this->prefix . $daux->getParams()['title'];

        $params = $daux->getParams();

        echo "Generating Tree...\n";
        $tree = $this->generateRecursive($daux->tree, $params);
        $tree['title'] = $main_title;

        echo "Getting already published pages...\n";
        $this->client = new Api($confluence['base_url'], $confluence['user'], $confluence['pass'], $confluence['space_id']);
        $all_published = $this->client->getHierarchy($confluence['ancestor_id']);

        echo "Finding Root Page...\n";
        $published = [];
        foreach ($all_published as $page) {
            if ($page['title'] == $main_title) {
                $published = $page;
                break;
            }
        }

        echo "Create placeholder pages...\n";
        $published = $this->createRecursive($confluence['ancestor_id'], $tree, $published);

        echo "Publishing updates...\n";
        $this->updateRecursive($confluence['ancestor_id'], $tree, $published);

        echo "Done !\n";
    }

    private function createRecursive($parent_id, $entry, $published)
    {
        //TODO :: remove deleted pages

        if (!array_key_exists('id', $published)) {
            if (array_key_exists('page', $entry)) {
                echo "Creating: " . $entry['file']->getUrl() . "\n";
                $published['version'] = 1;
                $published['id'] = $this->client->createPage($parent_id, $entry['title'], "The content will come very soon !");
            } else {
                echo "Creating Placeholder page: " . $entry['title'] . "\n";
                $published['version'] = 1;
                $published['id'] = $this->client->createPage($parent_id, $entry['title'], "");
            }
        }

        if (array_key_exists('children', $entry)) {
            foreach($entry['children'] as $child) {
                $pub = [];
                if (array_key_exists('children', $published) && array_key_exists($child['title'], $published['children'])) {
                    $pub = $published['children'][$child['title']];
                }

                $published['children'][$child['title']] = $this->createRecursive($published['id'], $child, $pub);
            }
        }

        return $published;
    }

    private function updateRecursive($parent_id, $entry, $published)
    {
        if (array_key_exists('id', $published) && array_key_exists('page', $entry)) {
            echo "Updating: " . $entry['file']->getUrl() . "\n";
            try {
                $this->client->updatePage(
                    $parent_id,
                    $published['id'],
                    $published['version'] + 1,
                    $entry['title'],
                    $entry['page']->getContent()
                );
            } catch (ClientException $e) {
                echo "-> Failed with message: " . $e->getResponse()->json()['message'] . "\n";
            }
        }

        if (array_key_exists('children', $entry)) {
            foreach($entry['children'] as $child) {
                $pub = [];
                if (array_key_exists('children', $published) && array_key_exists($child['title'], $published['children'])) {
                    $pub = $published['children'][$child['title']];
                }

                $this->updateRecursive($published['id'], $child, $pub);
            }
        }
    }

    private function generateRecursive(Entry $tree, array $params, $base_url = '')
    {
        $final = ['title' => $this->prefix . $tree->getTitle()];
        $params['base_url'] = $params['base_page'] = $base_url;

        $params['image'] = str_replace('<base_url>', $base_url, $params['image']);
        if ($base_url !== '') {
            $params['entry_page'] = $tree->getFirstPage();
        }
        foreach ($tree->value as $key => $node) {
            if ($node instanceof Directory) {
                $final['children'][$this->prefix . $node->getTitle()] = $this->generateRecursive($node, $params, '../' . $base_url);
            } else if ($node instanceof Content) {

                $params['request'] = $node->getUrl();
                $params['file_uri'] = $node->getName();

                $data = [
                    'title' => $this->prefix . $node->getTitle(),
                    'file' => $node,
                    'page' => MarkdownPage::fromFile($node, $params),
                ];

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
