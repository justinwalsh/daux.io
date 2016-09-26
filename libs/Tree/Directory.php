<?php namespace Todaymade\Daux\Tree;

use ArrayIterator;
use RuntimeException;

class Directory extends Entry implements \ArrayAccess, \IteratorAggregate
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

            if ($name[0] == '-') {
                if (is_numeric($name[1])) {
                    $exploded = explode('_', $name);
                    $buckets['down_numeric'][abs(substr($exploded[0], 1))][$key] = $entry;
                    continue;
                }

                $buckets['down'][$key] = $entry;
                continue;
            }

            if (is_numeric($name[0])) {
                $exploded = explode('_', $name);
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
        uasort($bucket, function (Entry $a, Entry $b) {
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
            throw new \RuntimeException('Could not retrieve configuration. Are you sure that your tree has a Root ?');
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

        if ($this->getConfig()->shouldInheritIndex() && $first_page = $this->seekFirstPage()) {
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
        if ($this instanceof self) {
            $index_key = $this->getConfig()['index_key'];
            if (isset($this->children[$index_key])) {
                return $this->children[$index_key];
            }
            foreach ($this->children as $node_key => $node) {
                if ($node instanceof Content) {
                    return $node;
                }
                if ($node instanceof self
                && strpos($node->getUri(), '.') !== 0
                && $childNode = $node->seekFirstPage()) {
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
            if ($node instanceof self && $page = $node->getFirstPage()) {
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
            } elseif ($node instanceof self) {
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

    /**
     * Whether a offset exists
     * @param mixed $offset An offset to check for.
     * @return bool true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->children);
    }

    /**
     * Offset to retrieve
     * @param mixed $offset The offset to retrieve.
     * @return Entry Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->children[$offset];
    }

    /**
     * Offset to set
     * @param mixed $offset The offset to assign the value to.
     * @param Entry $value The value to set.
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Entry) {
            throw new RuntimeException('The value is not of type Entry');
        }

        $this->addChild($value);
    }

    /**
     * Offset to unset
     * @param string $offset the offset to unset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->children[$offset]);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->children);
    }
}
