<?php namespace Todaymade\Daux\Tree;

use Todaymade\Daux\Config;

class Root extends Directory
{
    /** @var Config */
    protected $config;

    /** @var Entry */
    protected $activeNode;

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

    public function isHotPath(Entry $node = null) {
        if ($node == null) {
            return true;
        }

        if ($this->activeNode == null) {
            return false;
        }

        if ($node == $this->activeNode) {
            return true;
        }

        foreach ($this->activeNode->getParents() as $parent) {
            if ($node == $parent) {
                return true;
            }
        }

        return false;
    }

    public function setActiveNode(Entry $node) {
        $this->activeNode = $node;
    }
}
