<?php namespace Todaymade\Daux\Tree;

abstract class Entry
{
    /** @var string */
    protected $title;

    /** @var string */
    protected $name;

    /** @var Content */
    protected $index_page;

    /** @var Content */
    protected $first_page;

    /** @var string */
    protected $uri;

    /** @var Directory */
    protected $parent;

    /** @var string */
    protected $path;

    /** @var integer */
    protected $last_modified;

    /**
     * @param Directory $parent
     * @param string $uri
     * @param string $path
     * @param integer $last_modified
     */
    public function __construct(Directory $parent, $uri, $path = null, $last_modified = null)
    {
        $this->setUri($uri);
        $this->setParent($parent);

        if ($path !== null) {
            $this->path = $path;
        }

        if ($last_modified !== null) {
            $this->last_modified = $last_modified;
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        if ($this->parent) {
            $this->parent->removeChild($this);
        }

        $this->uri = $uri;

        if ($this->parent) {
            $this->parent->addChild($this);
        }
    }

    /**
     * @return Content
     */
    public function getIndexPage()
    {
        return $this->index_page;
    }

    /**
     * @param Content $index_page
     */
    public function setIndexPage($index_page)
    {
        $this->index_page = $index_page;
    }

    /**
     * @return Content|false
     */
    public function getFirstPage()
    {
        if ($this->first_page) {
            return $this->first_page;
        }

        if (!$this instanceof Directory) {
            return false;
        }

        // First we try to find a real page
        foreach ($this->getEntries() as $node) {
            if ($node instanceof Content) {
                // TODO :: this condition looks weird ...
                if (!$node->getParent() && $node->getTitle() == 'index') {
                    //the homepage should not count as first page
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

        return false;
    }

    /**
     * @param Content $first_page
     */
    public function setFirstPage($first_page)
    {
        $this->first_page = $first_page;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return Directory
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Return all parents starting with the root
     *
     * @return Directory[]
     */
    public function getParents()
    {
        $parents = [];
        if ($this->parent && !$this->parent instanceof Root) {
            $parents = $this->parent->getParents();
            $parents[] = $this->parent;
        }

        return $parents;
    }

    /**
     * @param Directory $parent
     */
    protected function setParent(Directory $parent)
    {
        if ($this->parent) {
            $this->parent->removeChild($this);
        }

        $parent->addChild($this);
        $this->parent = $parent;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $url = '';

        if ($this->getParent() && !$this->getParent() instanceof Root) {
            $url = $this->getParent()->getUrl() . '/' . $url;
        }

        $url .= $this->getUri();
        return $url;
    }

    public function dump()
    {
        return [
            'title' => $this->getTitle(),
            'type' => get_class($this),
            'name' => $this->getName(),
            'uri' => $this->getUri(),
            'url' => $this->getUrl(),
            'index' => $this->getIndexPage() ? $this->getIndexPage()->getUrl() : '',
            'first' => $this->getFirstPage() ? $this->getFirstPage()->getUrl() : '',
            'path' => $this->path
        ];
    }
}
