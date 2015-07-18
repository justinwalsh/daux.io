<?php namespace Todaymade\Daux\Format\Confluence;

use DOMDocument;
use Todaymade\Daux\DauxHelper;

class MarkdownPage extends \Todaymade\Daux\Format\Base\MarkdownPage
{
    public $attachments = [];

    protected function generatePage()
    {
        $page = parent::generatePage();

        //Embed images
        // We do it after generation so we can catch the images that were in html already
        $page = preg_replace_callback(
            "/<img\\s+[^>]*src=['\"]([^\"]*)['\"][^>]*>/",
            function($matches) {

                if ($result = $this->findImage($matches[1], $matches[0])) {
                    return $result;
                }

                return $matches[0];
            },
            $page
        );

        return $page;
    }

    private function findImage($src, $tag)
    {
        //for protocol relative or http requests : keep the original one
        if (substr($src, 0, strlen("http")) === "http" || substr($src, 0, strlen("//")) === "//") {
            return $src;
        }

        //Get the path to the file, relative to the root of the documentation
        $url = DauxHelper::getCleanPath(dirname($this->file->getUrl()) . '/' . $src);

        //Get any file corresponding to the right one
        $file = DauxHelper::getFile($this->params['tree'], $url);


        if ($file === false) {
            return false;
        }

        $filename = basename($file->getPath());

        //Add the attachment for later upload
        $this->attachments[] = ['filename' => $filename, 'file' => $file];

        return $this->createImageTag($filename, $this->getAttributes($tag));
    }

    private function getAttributes($tag)
    {
        $dom = new DOMDocument();
        $dom->loadHTML($tag);

        $img = $dom->getElementsByTagName('img')[0];

        $attributes = ['align', 'class', 'title', 'style', 'alt', 'height', 'width'];
        $used = [];
        foreach ($attributes as $attr) {
            if ($img->attributes->getNamedItem($attr)) {
                $used[$attr] = $img->attributes->getNamedItem($attr)->value;
            }
        }

        return $used;
    }

    private function createImageTag($filename, $attributes)
    {
        $img = "<ac:image";

        foreach ($attributes as $name => $value) {
            $img .= ' ac:' . $name . '="' . htmlentities($value, ENT_QUOTES, 'UTF-8', false) . '"';
        }

        $img .= "><ri:attachment ri:filename=\"$filename\" /></ac:image>";

        return $img;
    }
}
