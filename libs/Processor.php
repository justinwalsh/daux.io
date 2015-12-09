<?php namespace Todaymade\Daux;

use League\CommonMark\Environment;
use Symfony\Component\Console\Output\OutputInterface;
use Todaymade\Daux\Tree\Root;

class Processor
{
    /**
     * @var Daux
     */
    protected $daux;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var integer
     */
    protected $width;

    /**
     * @param Daux $daux
     * @param OutputInterface $output
     * @param integer $width
     */
    public function __construct(Daux $daux, OutputInterface $output, $width)
    {
        $this->daux = $daux;
        $this->output = $output;
        $this->width = $width;
    }

    /**
     * With this connection point, you can transform
     * the tree as you want, move pages, modify
     * pages and even add new ones.
     *
     * @param Root $root
     */
    public function manipulateTree(Root $root)
    {
    }

    /**
     * This connection point provides
     * a way to extend the Markdown
     * parser and renderer.
     *
     * @param Environment $environment
     */
    public function extendCommonMarkEnvironment(Environment $environment)
    {
    }

    /**
     * Provide new generators with this extension point. You
     * can simply return an array, the key is the format's
     * name, the value is a class name implementing the
     * `Todaymade\Daux\Format\Base\Generator` contract.
     * You can also replace base generators.
     *
     * @return string[]
     */
    public function addGenerators()
    {
        return [];
    }

    /**
     * Provide new content Types to be used during the generation
     * phase, with this you can change the markdown parser or add
     * a completely different file type.
     *
     * @return \Todaymade\Daux\ContentTypes\ContentType[]
     */
    public function addContentType()
    {
        return [];
    }
}
