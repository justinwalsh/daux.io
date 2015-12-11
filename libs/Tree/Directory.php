<?php namespace Todaymade\Daux\Tree;

use Todaymade\Daux\Daux;

class Directory extends Entry
{
    /** @var Entry[] */
    protected $children = [];

    /** @var Content */
    protected $first_page;

    public function sort()
    {
        // Separate the values into buckets to sort them separately
        $buckets = [
            'index' => [],
            'numeric' => [],
            'normal' => [],
            'down_numeric' => [],
            'down' => [],
        ];

        foreach ($this->children as $key => $entry) {
            $name = $entry->getName();

            if ($name == 'index' || $name == '_index') {
                $buckets['index'][$key] = $entry;
                continue;
            }

            if ($name[0] == "-") {
                if (is_numeric($name[1])) {
                    $exploded = explode("_", $name);
                    $buckets['down_numeric'][abs(substr($exploded[0], 1))][$key] = $entry;
                    continue;
                }

                $buckets['down'][$key] = $entry;
                continue;
            }

            if (is_numeric($name[0])) {
                $exploded = explode("_", $name);
                $buckets['numeric'][abs($exploded[0])][$key] = $entry;
                continue;
            }

            $buckets['normal'][$key] = $entry;
        }

        $final = [];
        foreach ($buckets as $name => $bucket) {
            if ($name == 'numeric' || $name == 'down_numeric') {
                ksort($bucket);
                foreach ($bucket as $sub_bucket) {
                    $final = $this->sortBucket($sub_bucket, $final);
                }
            } else {
                $final = $this->sortBucket($bucket, $final);
            }
        }

        $this->children = $final;
    }

    private function sortBucket($bucket, $final)
    {
        uasort($bucket, function(Entry $a, Entry $b) {
            return strcasecmp($a->getName(), $b->getName());
        });

        foreach ($bucket as $key => $value) {
            $final[$key] = $value;
        }

        return $final;
    }

    /**
     * @return Entry[]
     */
    public function getEntries()
    {
        return $this->children;
    }

    public function addChild(Entry $entry)
    {
        $this->children[$entry->getUri()] = $entry;
    }

    public function removeChild(Entry $entry)
    {
        unset($this->children[$entry->getUri()]);
    }

    /**
     * @return \Todaymade\Daux\Config
     */
    public function getConfig()
    {
        if (!$this->parent) {
            throw new \RuntimeException("Could not retrieve configuration. Are you sure that your tree has a Root ?");
        }

        return $this->parent->getConfig();
    }

    /**
     * @return Content|null
     */
    public function getIndexPage()
    {
        $index_key = $this->getConfig()['index_key'];

        if (isset($this->children[$index_key])) {
            return $this->children[$index_key];
        }

        /*
          If the inherit_index flag is set, then we seek child content
         */
        if ($this->getConfig()['mode'] == Daux::LIVE_MODE
            && !empty($this->getConfig()['live']['inherit_index'])
            && $first_page = $this->seekFirstPage()
        ) {
            return $first_page;
        }

        return null;
    }

    /**
     * Seek the first available page from descendants
     * @return Content|null
     */
    public function seekFirstPage()
    {
        if ($this instanceof Directory) {
            $index_key = $this->getConfig()['index_key'];
            if (isset($this->children[$index_key])) {
                return $this->children[$index_key];
            }
            foreach ($this->children as $node_key => $node) {
                if ($node instanceof Content) {
                    return $node;
                }
                if ($node instanceof Directory
                && strpos($node->getUri(), '.') !== 0
                && $childNode = $node->seekFirstPage() ) {
                    return $childNode;
                }
            }
        }
        return null;
    }

    /**
     * @return Content|null
     */
    public function getFirstPage()
    {
        if ($this->first_page) {
            return $this->first_page;
        }

        // First we try to find a real page
        foreach ($this->getEntries() as $node) {
            if ($node instanceof Content) {
                if ($this instanceof Root && $this->getIndexPage() == $node) {
                    // The homepage should not count as first page
                    continue;
                }

                $this->setFirstPage($node);
                return $node;
            }
        }

        // If we can't find one we check in the sub-directories
        foreach ($this->getEntries() as $node) {
            if ($node instanceof Directory && $page = $node->getFirstPage()) {
                $this->setFirstPage($page);
                return $page;
            }
        }

        return null;
    }

    /**
     * @param Content $first_page
     */
    public function setFirstPage($first_page)
    {
        $this->first_page = $first_page;
    }

    /**
     * Used when creating the navigation.
     * Hides folders without showable content
     *
     * @return bool
     */
    public function hasContent()
    {
        foreach ($this->getEntries() as $node) {
            if ($node instanceof Content) {
                return true;
            } elseif ($node instanceof Directory) {
                if ($node->hasContent()) {
                    return true;
                }
            }
        }

        return false;
    }

    public function dump()
    {
        $dump = parent::dump();

        $dump['index'] = $this->getIndexPage() ? $this->getIndexPage()->getUrl() : '';
        $dump['first'] = $this->getFirstPage() ? $this->getFirstPage()->getUrl() : '';

        foreach ($this->getEntries() as $entry) {
            $dump['children'][] = $entry->dump();
        }

        return $dump;
    }
}
