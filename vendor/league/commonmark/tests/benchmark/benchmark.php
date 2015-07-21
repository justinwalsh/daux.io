#!/usr/bin/env php
<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use League\CommonMark\CommonMarkConverter;
use Michelf\Markdown;
use Michelf\MarkdownExtra;

$markdown = file_get_contents(__DIR__ . '/' . 'sample.md');

$parsers = array(
    'CommonMark' => function ($markdown) {
        $parser = new CommonMarkConverter();
        $parser->convertToHtml($markdown);
    },
    'PHP Markdown' => function ($markdown) {
        Markdown::defaultTransform($markdown);
    },
    'PHP Markdown Extra' => function ($markdown) {
        MarkdownExtra::defaultTransform($markdown);
    },
    'Parsedown' => function ($markdown) {
        $parser = new Parsedown();
        $parser->text($markdown);
    }
);

$iterations = 20;
$results = array();
foreach ($parsers as $name => $parser) {
    $start = microtime(true);
    for ($i = 0; $i <= $iterations; $i++) {
        echo '.';
        $parser($markdown);
    }

    $results[$name] = (microtime(true) - $start) * 1000 / $iterations;
}

asort($results);

printf("\n\n");
printf("===================================\n");
printf("Here are the average parsing times:\n", $iterations);
printf("===================================\n");
foreach ($results as $name => $ms) {
    printf("%-18s | %4d ms\n", $name, $ms);
}

