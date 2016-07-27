<?php namespace Todaymade\Daux\Console;

use Symfony\Component\Console\Output\OutputInterface;

trait RunAction
{
    protected function runAction($title, OutputInterface $output, $width, \Closure $closure)
    {
        $output->write($title);

        $length = function_exists('mb_strlen') ? mb_strlen($title) : strlen($title);

        // 8 is the length of the label + 2 let it breathe
        $padding = $width - $length - 10;
        try {
            $response = $closure();
        } catch (\Exception $e) {
            $output->writeln(str_pad(' ', $padding) . '[ <fg=red>FAIL</fg=red> ]');
            throw $e;
        }
        $output->writeln(str_pad(' ', $padding) . '[  <fg=green>OK</fg=green>  ]');

        return $response;
    }
}
