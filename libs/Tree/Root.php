<?php namespace Todaymade\Daux\Tree;

use Todaymade\Daux\Config;

class Root extends Directory
{
    /** @var Config */
    protected $config;

    /**
     * The root doesn't have a parent
     *
     * @param string $uri
     */
    public function __construct(Config $config, $uri)
    {
        $this->setConfig($config);

        $this->setUri($uri);
        $this->path = $uri;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param Config $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }
}
