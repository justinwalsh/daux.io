<?php namespace Todaymade\Daux\Tree;

class Content extends Entry
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

        $content = $this->getContent();
        $sections = preg_split('/\s+-{3,}\s+/', $content, 2);

        // Only do it if we have two sections
        if (count($sections) != 2) {
            return;
        }

        // Parse the different attributes
        $lines = preg_split('/\n/', $sections[0]);
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed == '') continue; // skip empty lines
            if ($trimmed[0] == '#') continue; // can be taken as comments
            
            $re = "/^([-\\w]*)\\s*?:(.*)/";
            if (!preg_match($re, $trimmed, $parts)) break; //Break as soon as we have a line that doesn't match

            $key = strtolower(trim($parts[1]));
            $value = trim($parts[2]);
            $this->attributes[$key] = $value;
        }

        // Only remove the content if we have at least one attribute
        if (count($this->attributes) > 0) {
            $this->setContent($sections[1]);
        }
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
