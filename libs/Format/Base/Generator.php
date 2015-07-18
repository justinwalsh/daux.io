<?php namespace Todaymade\Daux\Format\Base;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Todaymade\Daux\Daux;

interface Generator
{
    public function generate(Daux $daux, InputInterface $input, OutputInterface $output, $width);
}
