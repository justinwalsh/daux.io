<?php namespace Todaymade\Daux\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Todaymade\Daux\Daux;

class DauxCommand extends SymfonyCommand
{
    protected function prepareDaux(InputInterface $input)
    {
        $daux = new Daux(Daux::STATIC_MODE);

        // Set the format if requested
        if ($input->hasOption('format') && $input->getOption('format')) {
            $daux->getParams()->setFormat($input->getOption('format'));
        }

        // Set the source directory
        if ($input->getOption('source')) {
            $daux->getParams()->setDocumentationDirectory($input->getOption('source'));
        }

        if ($input->getOption('themes')) {
            $daux->getParams()->setThemesDirectory($input->getOption('themes'));
        }

        $daux->initializeConfiguration($input->getOption('configuration'));

        if ($input->hasOption('delete') && $input->getOption('delete')) {
            $daux->getParams()['confluence']['delete'] = true;
        }

        return $daux;
    }
}
