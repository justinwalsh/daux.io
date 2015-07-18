<?php namespace Todaymade\Daux\Tree;

use Todaymade\Daux\DauxHelper;

class Content extends Entry
{
    /**
     * @var string
     */
    protected $content;

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
     * @param string $file
     * @return string
     */
    protected function getFilename($file)
    {
        return DauxHelper::pathinfo($file)['filename'];
    }
}
