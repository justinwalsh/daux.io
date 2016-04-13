<?php namespace Todaymade\Daux\ContentTypes\Markdown\TOC;

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
use Todaymade\Daux\DauxHelper;

class TOCProcessor implements DocumentProcessorInterface
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function hasAutoTOC()
    {
        return array_key_exists('auto_toc', $this->config) && $this->config['auto_toc'];
    }

    /**
     * @param Document $document
     *
     * @return void
     */
    public function processDocument(Document $document)
    {
        /** @var Element[] $tocs */
        $tocs = [];

        $headings = [];

        $walker = $document->walker();
        while ($event = $walker->next()) {
            $node = $event->getNode();

            if ($node instanceof Element && !$event->isEntering()) {
                $tocs[] = $node;
                continue;
            }

            if (!($node instanceof Heading) || !$event->isEntering()) {
                continue;
            }

            $id = $this->addId($node);

            $headings[] = new Entry($node, $id);
        }

        if (count($headings) && (count($tocs) || $this->hasAutoTOC())) {
            $generated = $this->generate($headings);

            if (count($tocs)) {
                foreach ($tocs as $toc) {
                    $toc->replaceWith($this->render($generated->getChildren()));
                }
            } else {
                $document->prependChild($this->render($generated->getChildren()));
            }

        }
    }

    protected function addId(Heading $node)
    {
        // If the node has an ID, no need to generate it
        $attributes = $node->getData('attributes', []);
        if (array_key_exists('id', $attributes) && !empty($attributes['id'])) {
            // TODO :: check for uniqueness

            return $attributes['id'];
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

        $text = 'page_' . DauxHelper::slug(trim($text));

        // TODO :: check for uniqueness
        $node->data['attributes']['id'] = $text;
    }

    /**
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
                } while ($heading->getLevel() <= $parent->getLevel() || $parent->getLevel() != 0);

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

            foreach ($this->cloneChildren($entry->getContent()) as $node) {
                $a->appendChild($node);
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
     * @param Heading $node
     * @return Node[]
     */
    protected function cloneChildren(Heading $node)
    {
        $deepCopy = new DeepCopy();

        $firstClone = clone $node;

        // We have no choice but to hack into the system to reset the parent, to avoid cloning the complete tree
        $method = new ReflectionMethod(get_class($firstClone), 'setParent');
        $method->setAccessible(true);
        $method->invoke($firstClone, null);

        return $deepCopy->copy($firstClone)->children();
    }
}
