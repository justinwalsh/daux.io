<?php namespace Todaymade\Daux\Format\Base\ContentTypes;

use Todaymade\Daux\Config;

interface ContentType
{
    public function __construct(Config $config);

    /**
     * Get the file extensions supported by this Content Type
     *
     * @return string[]
     */
    public function getExtensions();

    public function convert($html);
}
