<?php namespace Todaymade\Daux\Tree;

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

    /**
     * @return string
     */
    public function getContent()
    {
        if (!$this->content && !$this->manuallySetContent) {
            $this->content = file_get_contents($this->getPath());
        }

        if ($this->attributes === null) {
            $this->parseAttributes();
        }

        return $this->content;
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

        $frontMatter = new FrontMatter();

        $content = $this->getContent();
        if (substr($content, 0, 3) == "\xef\xbb\xbf") {
            $content = substr($content, 3);
        }

        $document = $frontMatter->parse($content);

        $this->attributes = array_replace_recursive($this->attributes, $document->getData());
        $this->setContent($document->getContent());
    }

    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

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
