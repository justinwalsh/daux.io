<?php namespace Todaymade\Daux\Tree;

use RuntimeException;
use Webuni\FrontMatter\FrontMatter;

class Content extends ContentAbstract
{
    /** @var string */
    protected $content;

    /** @var Content */
    protected $previous;

    /** @var Content */
    protected $next;

    /** @var array */
    protected $attributes;

    /** @var bool */
    protected $manuallySetContent = false;

    protected function getFrontMatter()
    {
        if ($this->manuallySetContent) {
            $content = $this->content;
        } else if (!$this->getPath()) {
            throw new RuntimeException("Empty content");
        } else {
            $content = file_get_contents($this->getPath());
        }

        $frontMatter = new FrontMatter();

        if (substr($content, 0, 3) == "\xef\xbb\xbf") {
            $content = substr($content, 3);
        }

        return $frontMatter->parse($content);
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->getFrontMatter()->getContent();
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->manuallySetContent = true;
        $this->content = $content;
    }

    /**
     * @return Content
     */
    public function getPrevious()
    {
        return $this->previous;
    }

    /**
     * @param Content $previous
     */
    public function setPrevious($previous)
    {
        $this->previous = $previous;
    }

    /**
     * @return Content
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @param Content $next
     */
    public function setNext($next)
    {
        $this->next = $next;
    }

    public function isIndex()
    {
        // At some point, it was recommended that
        // an index page starts with an underscore.
        // This is not mandatory anymore, both with
        // and without underscore are supported.
        return $this->name == 'index' || $this->name == '_index';
    }

    public function getTitle()
    {
        if ($title = $this->getAttribute('title')) {
            return $title;
        }

        return parent::getTitle();
    }

    protected function parseAttributes()
    {
        // We set an empty array first to
        // avoid a loop when "parseAttributes"
        // is called in "getContent"
        $this->attributes = [];

        $document = $this->getFrontMatter();
        $this->attributes = array_replace_recursive($this->attributes, $document->getData());
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Get one or all attributes for the content
     *
     * @param string|null $key
     * @return array|mixed|null
     */
    public function getAttribute($key = null)
    {
        if ($this->attributes === null) {
            $this->parseAttributes();
        }

        if (is_null($key)) {
            return $this->attributes;
        }

        if (!array_key_exists($key, $this->attributes)) {
            return null;
        }

        return $this->attributes[$key];
    }

    public function dump()
    {
        $dump = parent::dump();

        $dump['prev'] = $this->getPrevious() ? $this->getPrevious()->getUrl() : '';
        $dump['next'] = $this->getNext() ? $this->getNext()->getUrl() : '';

        return $dump;
    }
}
