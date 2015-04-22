<?php namespace Todaymade\Daux\Tree;

use Todaymade\Daux\DauxHelper;

class Content extends Entry
{
    public $title;

    public function __construct($path = '', $parents = array())
    {
        parent::__construct($path, $parents);

        $this->value = $this->uri;
    }

    protected function getFilename($file)
    {
        $file = DauxHelper::pathinfo($file);
        return $file['filename'];
    }
}
