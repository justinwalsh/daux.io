<?php namespace Todaymade\Daux\Format\HTML\ContentTypes\Markdown\TOC;

use League\CommonMark\Block\Element\Heading;

class Entry
{
    protected $content;
    protected $level;
    protected $parent = null;
    protected $children = [];

    public function __construct(Heading $content)
    {
        $this->content = $content;
        $this->level = $content->getLevel();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->content->data['attributes']['id'];
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return Entry
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return Heading
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return Entry[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param Entry $parent
     * @param bool $addChild
     */
    public function setParent(Entry $parent, $addChild = true)
    {
        $this->parent = $parent;
        if ($addChild) {
            $parent->addChild($this);
        }
    }

    /**
     * @param Entry $child
     */
    public function addChild(Entry $child)
    {
        $child->setParent($this, false);
        $this->children[] = $child;
    }

    public function toString()
    {
        return $this->getLevel() . ' - ' . $this->getId();
    }
}
