<?php namespace Todaymade\Daux\Tree;

class Raw extends Entry
{
    public function __construct($path = '', $parents = array())
    {
        parent::__construct($path, $parents);

        $this->value = $this->uri;
    }
}
