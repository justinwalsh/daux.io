<?php namespace Todaymade\Daux\Tree;

use Todaymade\Daux\DauxHelper;

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

    /** @var string */
    protected $local_path;

    /** @var integer */
    protected $last_modified;

    /** @var array */
    protected $parents;

    /**
     * @param string $path
     * @param array $parents
     */
    public function __construct($path = '', $parents = array())
    {
        $this->setPath($path);
        $this->setParents($parents);
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
        $this->uri = $uri;
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

        foreach ($this->value as $node) {
            if ($node instanceof Content) {
                if (!count($node->getParents()) && $node->title == 'index') {
                    //the homepage should not count as first page
                    continue;
                }

                $this->first_page = $node;
                return $node;
            }
        }

        foreach ($this->value as $node) {
            if ($node instanceof Directory && $page = $node->getFirstPage()) {
                $this->first_page = $page;
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
     * @return array
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * @param array $parents
     */
    public function setParents($parents)
    {
        $this->parents = $parents;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->local_path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        if (!isset($path) || $path == '' || !file_exists($path)) {
            return;
        }
        $this->local_path = $path;
        $this->last_modified = filemtime($path);
        $this->name = DauxHelper::pathinfo($path)['filename'];
        $this->title = $this->getTitleInternal($this->name);
        $this->uri = $this->getUrlInternal($this->getFilename($path));
        $this->index_page = false;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $url = '';
        foreach ($this->parents as $node) {
            $url .= $node->uri . '/';
        }
        $url .= $this->uri;
        return $url;
    }

    /**
     * @param string $file
     * @return string
     */
    protected function getFilename($file)
    {
        $parts = explode('/', $file);
        return end($parts);
    }

    /**
     * @param string $filename
     * @return string
     */
    protected function getTitleInternal($filename)
    {
        $filename = explode('_', $filename);
        if ($filename[0] == '' || is_numeric($filename[0])) {
            unset($filename[0]);
        } else {
            $t = $filename[0];
            if ($t[0] == '-') {
                $filename[0] = substr($t, 1);
            }
        }
        $filename = implode(' ', $filename);
        return $filename;
    }

    /**
     * @param string $filename
     * @return string
     */
    protected function getUrlInternal($filename)
    {
        $filename = explode('_', $filename);
        if ($filename[0] == '' || is_numeric($filename[0])) {
            unset($filename[0]);
        } else {
            $t = $filename[0];
            if ($t[0] == '-') {
                $filename[0] = substr($t, 1);
            }
        }
        $filename = implode('_', $filename);
        return $filename;
    }
}
