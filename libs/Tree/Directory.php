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
        uasort($this->children, array($this, 'compareEntries'));
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
        if (
            $this->getConfig()['mode'] == Daux::LIVE_MODE
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
    public function seekFirstPage(){
      if( $this instanceof Directory ){
        $index_key = $this->getConfig()['index_key'];
        if (isset($this->children[$index_key])) {
          return $this->children[$index_key];
        }
        foreach( $this->children AS $node_key => $node ){
          if( $node instanceof Content ){
            return $node;
          }
          if(
            $node instanceof Directory
            && strpos($node->getUri(), '.') !== 0
            && $childNode = $node->seekFirstPage() ){
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

    private function compareEntries($a, $b)
    {
        $name_a = explode('_', $a->name);
        $name_b = explode('_', $b->name);
        if (is_numeric($name_a[0])) {
            $a = intval($name_a[0]);
            if (is_numeric($name_b[0])) {
                $b = intval($name_b[0]);
                if (($a >= 0) == ($b >= 0)) {
                    $a = abs($a);
                    $b = abs($b);
                    if ($a == $b) {
                        return (strcasecmp($name_a[1], $name_b[1]));
                    }
                    return ($a > $b) ? 1 : -1;
                }
                return ($a >= 0) ? -1 : 1;
            }
            $t = $name_b[0];
            if ($t && $t[0] === '-') {
                return -1;
            }
            return ($a < 0) ? 1 : -1;
        } else {
            if (is_numeric($name_b[0])) {
                $b = intval($name_b[0]);
                if ($b >= 0) {
                    return 1;
                }
                $t = $name_a[0];
                if ($t && $t[0] === '-') {
                    return 1;
                }
                return ($b >= 0) ? 1 : -1;
            }
            $p = $name_a[0];
            $q = $name_b[0];
            if (($p && $p[0] === '-') == ($q && $q[0] === '-')) {
                return strcasecmp($p, $q);
            } else {
                return ($p[0] === '-') ? 1 : -1;
            }
        }
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
