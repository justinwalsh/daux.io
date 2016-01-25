<?php namespace Todaymade\Daux\Format\Confluence;

use Todaymade\Daux\Format\Base\EmbedImages;
use Todaymade\Daux\Tree\Raw;

class ContentPage extends \Todaymade\Daux\Format\Base\ContentPage
{
    public $attachments = [];

    protected function generatePage()
    {
        $content = parent::generatePage();

        //Embed images
        // We do it after generation so we can catch the images that were in html already
        $content = (new EmbedImages($this->params['tree']))
            ->embed(
                $content,
                $this->file,
                function ($src, array $attributes, Raw $file) {
                    $filename = basename($file->getPath());

                    //Add the attachment for later upload
                    $this->attachments[$filename] = ['filename' => $filename, 'file' => $file];

                    return $this->createImageTag($filename, $attributes);
                }
            );

        return $content;
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
