<?php namespace Todaymade\Daux\Tree;

use Todaymade\Daux\DauxHelper;

class Content extends Entry
{
	protected $content;

    public function __construct($path = '', $parents = array())
    {
        parent::__construct($path, $parents);

        $this->value = $this->uri;
    }
	
	public function getContent()
	{
		if (!$this->content) {
			$this->content = file_get_contents($this->getPath());
		}
		
		return $this->content;
	}
	
	public function setContent($content)
	{
		$this->content = $content;
	}

    protected function getFilename($file)
    {
        return DauxHelper::pathinfo($file)['filename'];
    }
}
