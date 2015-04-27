<?php namespace Todaymade\Daux\Tree;

use Todaymade\Daux\DauxHelper;

abstract class Entry
{
    protected $title;
    protected $name;
    protected $index_page;
    protected $first_page;
    protected $uri;
    protected $local_path;
    protected $last_modified;
    protected $parents;

    public function __construct($path = '', $parents = array())
    {
        if (!isset($path) || $path == '' || !file_exists($path)) {
            return;
        }
        $this->local_path = $path;
        $this->parents = $parents;
        $this->last_modified = filemtime($path);
        $this->name = DauxHelper::pathinfo($path)['filename'];
        $this->title = $this->getTitleInternal($this->name);
        $this->uri = $this->getUrlInternal($this->getFilename($path));
        $this->index_page = false;
    }
    public function getName()
    {
        return $this->name;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getUrl()
    {
        $url = '';
        foreach ($this->parents as $node) {
            $url .= $node->uri . '/';
        }
        $url .= $this->uri;
        return $url;
    }

    public function getIndexPage()
    {
        return $this->index_page;
    }

    public function setIndexPage($index_page)
    {
        $this->index_page = $index_page;
    }

    /**
     * @return Entry
     */
    public function getFirstPage()
    {
        if ($this->first_page) {
            return $this->first_page;
        }

        if ($this instanceof Directory) {
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
        }
        return false;
    }

    public function setFirstPage($first_page)
    {
        $this->first_page = $first_page;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getParents()
    {
        return $this->parents;
    }

    public function getPath()
    {
        return $this->local_path;
    }

    public function write($content)
    {
        if (!is_writable($this->local_path)) {
            return false;
        }

        file_put_contents($this->local_path, $content);
        return true;
    }

    protected function getFilename($file)
    {
        $parts = explode('/', $file);
        return end($parts);
    }

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
