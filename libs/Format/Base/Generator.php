<?php namespace Todaymade\Daux\Format\Base;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Todaymade\Daux\Daux;

interface Generator
{
    /**
     * @param Daux $daux
     */
    public function __construct(Daux $daux);

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param int $width
     * @return mixed
     */
    public function generateAll(InputInterface $input, OutputInterface $output, $width);

    /**
     * @return array
     */
    public function getContentTypes();
}
