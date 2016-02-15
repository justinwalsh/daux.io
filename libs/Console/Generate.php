<?php namespace Todaymade\Daux\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Todaymade\Daux\Daux;

class Generate extends SymfonyCommand
{
    protected function configure()
    {
        $description = 'Destination folder, relative to the working directory';

        $this
            ->setName('generate')
            ->setDescription('Generate documentation')
            ->addOption('configuration', 'c', InputOption::VALUE_REQUIRED, 'Configuration file')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Output format, html or confluence', 'html')
            ->addOption('processor', 'p', InputOption::VALUE_REQUIRED, 'Manipulations on the tree')
            ->addOption('source', 's', InputOption::VALUE_REQUIRED, 'Where to take the documentation from')
            ->addOption('delete', null, InputOption::VALUE_NONE, 'Delete pages not linked to a documentation page (confluence)')
            ->addOption('destination', 'd', InputOption::VALUE_REQUIRED, $description, 'static');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $daux = $this->prepareDaux($input);

        $width = $this->getApplication()->getTerminalDimensions()[0];

        // Instiantiate the processor if one is defined
        $this->prepareProcessor($daux, $input, $output, $width);

        // Generate the tree
        $daux->generateTree();

        // Generate the documentation
        $daux->getGenerator()->generateAll($input, $output, $width);
    }

    protected function prepareDaux(InputInterface $input)
    {
        $daux = new Daux(Daux::STATIC_MODE);

        // Set the format if requested
        if ($input->getOption('format')) {
            $daux->getParams()['format'] = $input->getOption('format');
        }

        // Set the source directory
        if ($input->getOption('source')) {
            $daux->getParams()['docs_directory'] = $input->getOption('source');
        }

        $daux->setDocumentationPath($daux->getParams()['docs_directory']);

        $daux->setThemesPath($daux->getParams()['themes_directory']);

        $daux->initializeConfiguration($input->getOption('configuration'));

        if ($input->getOption('delete')) {
            $daux->getParams()['confluence']['delete'] = true;
        }

        return $daux;
    }

    protected function prepareProcessor(Daux $daux, InputInterface $input, OutputInterface $output, $width)
    {
        if ($input->getOption('processor')) {
            $daux->getParams()['processor'] = $input->getOption('processor');
        }

        $class = $daux->getProcessorClass();
        if (!empty($class)) {
            $daux->setProcessor(new $class($daux, $output, $width));
        }
    }
}
