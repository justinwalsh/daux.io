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
            ->addOption('processor', 'p', InputArgument::OPTIONAL, 'Manipulations on the tree')
            ->addOption('destination', 'd', InputArgument::OPTIONAL, $description, 'static');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $daux = new Daux(Daux::STATIC_MODE);
        $daux->initialize($input->getOption('configuration'));

        $width = $this->getApplication()->getTerminalDimensions()[0];

        // Instiantiate the processor if one is defined
        $this->prepareProcessor($daux, $input, $output, $width);

        // Improve the tree with a processor
        $daux->getProcessor()->manipulateTree($daux->tree);

        // Set the format if requested
        if ($input->getOption('format')) {
            $daux->getParams()['format'] = $input->getOption('format');
        }

        // Generate the documentation
        $daux->getGenerator()->generateAll($input, $output, $width);
    }

    protected function prepareProcessor(Daux $daux, InputInterface $input, OutputInterface $output, $width)
    {
        if ($input->getOption('processor')) {
            $daux->getParams()['processor'] = $input->getOption('processor');
        }

        $class = $daux->getProcessorClass();
        if ($class) {
            $daux->setProcessor(new $class($daux, $output, $width));
        }
    }
}
