<?php namespace Todaymade\Daux\Format\Confluence;

use Todaymade\Daux\DauxHelper;

class MarkdownPage extends \Todaymade\Daux\Format\Base\MarkdownPage
{
    protected function generatePage()
    {
        $page = parent::generatePage();

        //Embed images
        $page = preg_replace_callback(
            "/<img\\s+[^>]*src=['\"]([^\"]*)['\"][^>]*>/",
            function ($matches) {
                return str_replace($matches[1], $this->findImage($matches[1]), $matches[0]);
            },
            $page
        );

        return $page;
    }

    private function findImage($src)
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
            return $src;
        }

        $encoded = base64_encode(file_get_contents($file->getPath()));
        $extension =  pathinfo($file->getPath(), PATHINFO_EXTENSION);

        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            return $src;
        }

        return "data:image/$extension;base64,$encoded";
    }
}
