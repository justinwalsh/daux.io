<?php namespace Todaymade\Daux\Format\HTML\ContentTypes\Markdown\TOC;

class RootEntry extends Entry
{
    public function __construct()
    {
        $this->content = null;
        $this->level = 0;
    }

    /**
     * @return Entry
     */
    public function getParent()
    {
        throw new \RuntimeException('No Parent Exception');
    }
}
