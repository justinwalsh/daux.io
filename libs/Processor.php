<?php namespace Todaymade\Daux;

use League\CommonMark\Environment;
use Symfony\Component\Console\Output\OutputInterface;
use Todaymade\Daux\Tree\Directory;

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

    public function __construct(Daux $daux, OutputInterface $output, $width)
    {
        $this->daux = $daux;
        $this->output = $output;
        $this->width = $width;
    }

    public function manipulateTree(Directory $root)
    {
    }

    public function extendCommonMarkEnvironment(Environment $environment)
    {
    }
}
