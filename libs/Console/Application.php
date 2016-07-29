<?php namespace Todaymade\Daux\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputInterface;

class Application extends SymfonyApplication
{
    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN') {
        parent::__construct($name, $version);

        $this->add(new Generate());
        $this->add(new Serve());
        $this->setDefaultCommand('generate');
    }
}
