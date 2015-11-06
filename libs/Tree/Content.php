<?php namespace Todaymade\Daux\Tree;

class Content extends Entry
{
    /**
     * @var string
     */
    protected $content;

    /**
     * @var Content
     */
    protected $previous;

    /**
     * @var Content
     */
    protected $next;

    /**
     * @return string
     */
    public function getContent()
    {
        if (!$this->content) {
            $this->content = file_get_contents($this->getPath());
        }

        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
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
        return $this->name == 'index' || $this->name == '_index';
    }

    public function dump()
    {
        $dump = parent::dump();

        $dump['prev'] = $this->getPrevious() ? $this->getPrevious()->getUrl() : '';
        $dump['next'] = $this->getNext() ? $this->getNext()->getUrl() : '';

        return $dump;
    }
}
