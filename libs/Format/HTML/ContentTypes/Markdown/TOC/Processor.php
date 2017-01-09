<?php namespace Todaymade\Daux\Format\HTML\ContentTypes\Markdown\TOC;

use DeepCopy\DeepCopy;
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\Heading;
use League\CommonMark\Block\Element\ListBlock;
use League\CommonMark\Block\Element\ListData;
use League\CommonMark\Block\Element\ListItem;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\DocumentProcessorInterface;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Node\Node;
use ReflectionMethod;
use Todaymade\Daux\Config;
use Todaymade\Daux\ContentTypes\Markdown\TableOfContents;
use Todaymade\Daux\DauxHelper;

class Processor implements DocumentProcessorInterface
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function hasAutoTOC()
    {
        return array_key_exists('html', $this->config) && array_key_exists('auto_toc', $this->config['html']) && $this->config['html']['auto_toc'];
    }

    /**
     * @param Document $document
     *
     * @return void
     */
    public function processDocument(Document $document)
    {
        /** @var TableOfContents[] $tocs */
        $tocs = [];

        $headings = [];

        $walker = $document->walker();
        while ($event = $walker->next()) {
            $node = $event->getNode();

            if ($node instanceof TableOfContents && !$event->isEntering()) {
                $tocs[] = $node;
                continue;
            }

            if (!($node instanceof Heading) || !$event->isEntering()) {
                continue;
            }

            $this->ensureHeadingHasId($node);
            $headings[] = new Entry($node);
        }

        if (count($headings) && (count($tocs) || $this->hasAutoTOC())) {
            $generated = $this->generate($headings);

            if (count($tocs)) {
                foreach ($tocs as $toc) {
                    $toc->appendChild($this->render($generated->getChildren()));
                }
            } else {
                $document->prependChild($this->render($generated->getChildren()));
            }
        }
    }

    /**
     * @param Heading $node
     */
    protected function ensureHeadingHasId(Heading $node)
    {
        // If the node has an ID, no need to generate it
        $attributes = $node->getData('attributes', []);
        if (array_key_exists('id', $attributes) && !empty($attributes['id'])) {
            // TODO :: check for uniqueness

            return;
        }

        // Well, seems we have to generate an ID

        $walker = $node->walker();
        $inside = [];
        while ($event = $walker->next()) {
            $insideNode = $event->getNode();

            if ($insideNode instanceof Heading) {
                continue;
            }

            $inside[] = $insideNode;
        }

        $text = '';
        foreach ($inside as $other) {
            if ($other instanceof Text) {
                $text .= ' ' . $other->getContent();
            }
        }

        $text = 'page_' . urlencode(trim($text));

        // TODO :: check for uniqueness
        $node->data['attributes']['id'] = $text;
    }

    /**
     * Make a tree of the list of headings
     *
     * @param Entry[] $headings
     * @return RootEntry
     */
    public function generate($headings)
    {
        /** @var Entry $previous */
        $root = $previous = new RootEntry();
        foreach ($headings as $heading) {
            if ($heading->getLevel() < $previous->getLevel()) {
                $parent = $previous;
                do {
                    $parent = $parent->getParent();
                } while ($heading->getLevel() <= $parent->getLevel() && $parent->getLevel() != 0);

                $parent->addChild($heading);
                $previous = $heading;
                continue;
            }


            if ($heading->getLevel() > $previous->getLevel()) {
                $previous->addChild($heading);
                $previous = $heading;
                continue;
            }

            //if ($heading->getLevel() == $previous->getLevel()) {
            $previous->getParent()->addChild($heading);
            $previous = $heading;
            continue;
            //}
        }

        return $root;
    }

    /**
     * @param Entry[] $entries
     * @return ListBlock
     */
    protected function render(array $entries)
    {
        $data = new ListData();
        $data->type = ListBlock::TYPE_UNORDERED;

        $list = new ListBlock($data);
        $list->data['attributes']['class'] = 'TableOfContents';

        foreach ($entries as $entry) {
            $item = new ListItem($data);

            $a = new Link('#' . $entry->getId());

            $content = $entry->getContent();
            if ($content != null) {
                foreach ($this->cloneChildren($content) as $node) {
                    $a->appendChild($node);
                }
            }

            $p = new Paragraph();
            $p->appendChild($a);

            $item->appendChild($p);

            if (!empty($entry->getChildren())) {
                $item->appendChild($this->render($entry->getChildren()));
            }

            $list->appendChild($item);
        }

        return $list;
    }

    /**
     * Set the specified property to null on the object.
     *
     * @param Heading $object The object to modify
     * @param string $property The property to nullify
     */
    protected function setNull(Heading $object, $property)
    {
        $prop = new \ReflectionProperty(get_class($object), $property);
        $prop->setAccessible(true);
        $prop->setValue($object, null);
    }

    /**
     * @param Heading $node
     * @return Node[]
     */
    protected function cloneChildren(Heading $node)
    {
        $firstClone = clone $node;

        // We have no choice but to hack into the
        // system to reset the parent, previous and next
        $this->setNull($firstClone, 'parent');
        $this->setNull($firstClone, 'previous');
        $this->setNull($firstClone, 'next');

        // Also, the child elements need to know the next parents
        foreach ($firstClone->children() as $subnode) {
            $method = new ReflectionMethod(get_class($subnode), 'setParent');
            $method->setAccessible(true);
            $method->invoke($subnode, $firstClone);
        }

        $deepCopy = new DeepCopy();

        return $deepCopy->copy($firstClone)->children();
    }
}
