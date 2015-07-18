<?php namespace Todaymade\Daux\Generator;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Todaymade\Daux\Daux;
use Todaymade\Daux\Format\HTML\Generator as HTMLGenerator;
use Todaymade\Daux\Format\Confluence\Generator as ConfluenceGenerator;

class Command extends SymfonyCommand
{
    protected function configure()
    {
        $description = 'Destination folder, relative to the working directory';

        $this
            ->setName('generate')
            ->setDescription('Generate documentation')
            ->addOption('configuration', 'c', InputArgument::OPTIONAL, 'Configuration file')
            ->addOption('format', 'f', InputArgument::OPTIONAL, 'Output format, html or confluence', 'html')
            ->addOption('processor', 'p', InputArgument::OPTIONAL, 'Manipulations on the tree', 'none')
            ->addOption('destination', 'd', InputArgument::OPTIONAL, $description, 'static');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $daux = new Daux(Daux::STATIC_MODE);
        $daux->initialize($input->getOption('configuration'));

        $width = $this->getApplication()->getTerminalDimensions()[0];

        $processor = $input->getOption('processor');
        if (!empty($processor) && $processor != 'none') {
            $this->prepareProcessor($processor, $daux, $output, $width);
        }

        // Improve the tree with a processor
        $daux->getProcessor()->manipulateTree($daux->tree);

        $this->launchGeneration($daux, $input, $output, $width);
    }

    protected function prepareProcessor($processor, Daux $daux, OutputInterface $output, $width)
    {
        $class = "\\Todaymade\\Daux\\Extension\\" . $processor;
        if (class_exists($class)) {
            $daux->setProcessor(new $class($daux, $output, $width));
        } elseif (file_exists($processor)) {
            include $processor;
        }
    }

    protected function launchGeneration(Daux $daux, InputInterface $input, OutputInterface $output, $width)
    {
        $generators = $daux->getGenerators();

        $format = strtolower($input->getOption('format'));
        if (empty($format)) {
            $format = 'html';
        }

        if (!array_key_exists($format, $generators)) {
            throw new \RuntimeException("The format '$format' doesn't exist, did you forget to set your processor ?");
        }

        $class = $generators[$format];
        if (!class_exists($class)) {
            throw new \RuntimeException("Class '$class' not found. We cannot use it as a Generator");
        }

        $interface = 'Todaymade\Daux\Format\Base\Generator';
        if (!in_array('Todaymade\Daux\Format\Base\Generator', class_implements($class))) {
            throw new \RuntimeException("the class '$class' does not implement the '$interface' interface");
        }

        (new $class())->generate($daux, $input, $output, $width);
    }
}
