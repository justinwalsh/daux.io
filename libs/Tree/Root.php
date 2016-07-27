<?php namespace Todaymade\Daux\Tree;

use Todaymade\Daux\Config;

class Root extends Directory
{
    /** @var Config */
    protected $config;

    /**
     * The root doesn't have a parent
     */
    public function __construct(Config $config)
    {
        $this->setConfig($config);

        $this->setUri($config->getDocumentationDirectory());
        $this->path = $config->getDocumentationDirectory();
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
