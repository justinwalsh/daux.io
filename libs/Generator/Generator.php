<?php namespace Todaymade\Daux\Generator;

use Todaymade\Daux\Daux;
use Todaymade\Daux\Format\HTML\Generator as HTMLGenerator;
use Todaymade\Daux\Format\Confluence\Generator as ConfluenceGenerator;

class Generator
{
    public function generate($options)
    {
        $daux = new Daux(Daux::STATIC_MODE);
        $daux->initialize(null, $options['config']);

        switch(strtolower($options['format'])) {
            case 'confluence':
                (new ConfluenceGenerator())->generate($daux, $options['destination']);
                break;
            case 'html':
            default:
                (new HTMLGenerator())->generate($daux, $options['destination']);
        }

    }
}
