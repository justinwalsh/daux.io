<?php namespace Todaymade\Daux\Format\Base;

use Todaymade\Daux\Exception;

abstract class RawPage implements Page
{
    protected $file;

    public function __construct($filename)
    {
        $this->file = $filename;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getContent()
    {
        throw new Exception("you should not use this method to show a raw content");
    }
}
