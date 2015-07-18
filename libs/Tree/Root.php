<?php namespace Todaymade\Daux\Tree;

class Root extends Directory
{
    /**
     * The root doesn't have a parent
     *
     * @param Directory $uri
     */
    public function __construct($uri)
    {
        $this->setUri($uri);
        $this->path = $uri;
    }
}
