<?php namespace Todaymade\Daux\Format\Base;

use Todaymade\Daux\Tree\ComputedRaw;

abstract class ComputedRawPage implements Page
{
    protected $raw;

    public function __construct(ComputedRaw $content)
    {
        $this->raw = $content;
    }

    public function getContent()
    {
        return $this->raw->getContent();
    }

    public function getPureContent()
    {
        return $this->raw->getContent();
    }
}
