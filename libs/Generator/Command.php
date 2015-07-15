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
			if (file_exists($processor)) {
				include $processor;
			}
		}

        switch(strtolower($input->getOption('format'))) {
            case 'confluence':
                (new ConfluenceGenerator())->generate($daux, $output, $width);
                break;
            case 'html':
            default:
                (new HTMLGenerator())->generate($daux, $input->getOption('destination'), $output, $width);
        }
    }
}
