<?php namespace Todaymade\Daux\Format\HTMLFile;

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
                    $content =  base64_encode(file_get_contents($file->getPath()));
                    $attr = '';
                    foreach ($attributes as $name => $value) {
                        $attr .= ' ' . $name . '="' . htmlentities($value, ENT_QUOTES, 'UTF-8', false) . '"';
                    }

                    return "<img $attr src=\"data:image/png;base64,$content\"/>";
                }
            );

        return $content;
    }
}
