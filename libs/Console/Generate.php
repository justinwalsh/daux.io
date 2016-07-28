<?php namespace Todaymade\Daux\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Todaymade\Daux\Daux;
use Todaymade\Daux\Server\Server;

class Generate extends SymfonyCommand
{
    protected function configure()
    {
        $description = 'Destination folder, relative to the working directory';

        $this
            ->setName('generate')
            ->setDescription('Generate documentation')

            ->addOption('configuration', 'c', InputOption::VALUE_REQUIRED, 'Configuration file')
            ->addOption('source', 's', InputOption::VALUE_REQUIRED, 'Where to take the documentation from')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'Output format, html or confluence', 'html')
            ->addOption('processor', 'p', InputOption::VALUE_REQUIRED, 'Manipulations on the tree')

            // Serve the current documentation
            ->addOption('serve', null, InputOption::VALUE_NONE, 'Serve the current directory')
            ->addOption('host', null, InputOption::VALUE_REQUIRED, 'The host to serve on', 'localhost')
            ->addOption('port', null, InputOption::VALUE_REQUIRED, 'The port to serve on', 8085)


            // Confluence format only
            ->addOption('delete', null, InputOption::VALUE_NONE, 'Delete pages not linked to a documentation page (confluence)')

            // HTML Format only
            ->addOption('destination', 'd', InputOption::VALUE_REQUIRED, $description, 'static')
            ->addOption('search', null, InputOption::VALUE_NONE, 'Generate full text search')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $daux = $this->prepareDaux($input);

        if ($input->getOption('serve')) {
            $this->serve($daux, $input);
            return;
        }

        $width = $this->getApplication()->getTerminalDimensions()[0];

        // Instiantiate the processor if one is defined
        $this->prepareProcessor($daux, $input, $output, $width);

        // Generate the tree
        $daux->generateTree();

        // Generate the documentation
        $daux->getGenerator()->generateAll($input, $output, $width);
    }

    protected function serve(Daux $daux, InputInterface $input)
    {
        // Daux can only serve HTML
        $daux->getParams()->setFormat('html');

        //TODO :: support configuration and processor

        Server::runServer($daux->getParams(), $input->getOption('host'), $input->getOption('port'));
    }

    protected function prepareDaux(InputInterface $input)
    {
        $daux = new Daux(Daux::STATIC_MODE);

        // Set the format if requested
        if ($input->getOption('format')) {
            $daux->getParams()->setFormat($input->getOption('format'));
        }

        // Set the source directory
        if ($input->getOption('source')) {
            $daux->getParams()->setDocumentationDirectory($input->getOption('source'));
        }

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
