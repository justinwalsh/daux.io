<?php

/*
 * This class is inspired from Composer's compiler
 * @see https://github.com/composer/composer/blob/master/src/Composer/Compiler.php
 */

namespace Todaymade\Daux;

use Symfony\Component\Finder\Finder;

/**
 * The Compiler class compiles daux into a phar
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Stéphane Goetz <stephane.goetz@onigoetz.ch>
 */
class Compiler
{
    /**
     * Compiles composer into a single phar file
     *
     * @throws \RuntimeException
     * @param  string $pharFile The full path to the file to create
     */
    public function compile($pharFile = 'daux.phar')
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $phar = new \Phar($pharFile, 0, 'daux.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        $phar->startBuffering();

        // Daux
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->notName('Compiler.php')
            ->in(__DIR__ . '/../templates')
            ->in(__DIR__);

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        // Composer libraries
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->exclude('Tests')
            ->in(__DIR__ . '/../vendor/symfony/console')
            ->in(__DIR__ . '/../vendor/symfony/polyfill-mbstring')
            ->in(__DIR__ . '/../vendor/guzzlehttp/guzzle/src/')
            ->in(__DIR__ . '/../vendor/guzzlehttp/ringphp/src/')
            ->in(__DIR__ . '/../vendor/guzzlehttp/streams/src/')
            ->in(__DIR__ . '/../vendor/league/commonmark/src/')
            ->in(__DIR__ . '/../vendor/league/plates/src/')
            ->in(__DIR__ . '/../vendor/react/promise/src/')
            ->in(__DIR__ . '/../vendor/webuni/commonmark-table-extension/src/');

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        // Composer autoload
        $this->addComposer($phar);
        $this->addBinary($phar);

        // Stubs
        $phar->setStub($this->getStub());

        $phar->stopBuffering();

        $this->addFile($phar, new \SplFileInfo(__DIR__ . '/../LICENSE'), false);

        chmod($pharFile, 0775);

        unset($phar);
    }

    private function addFile($phar, $file, $strip = true)
    {
        $path = strtr(str_replace(dirname(__DIR__) . DIRECTORY_SEPARATOR, '', $file->getRealPath()), '\\', '/');

        $content = file_get_contents($file);
        if ($strip) {
            $content = $this->stripWhitespace($content);
        } elseif ('LICENSE' === basename($file)) {
            $content = "\n" . $content . "\n";
        }

        $phar->addFromString($path, $content);
    }

    private function addComposer($phar)
    {
        $this->addFile($phar, new \SplFileInfo(__DIR__ . '/../vendor/autoload.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__ . '/../vendor/composer/autoload_classmap.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__ . '/../vendor/composer/autoload_files.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__ . '/../vendor/composer/autoload_namespaces.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__ . '/../vendor/composer/autoload_real.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__ . '/../vendor/composer/ClassLoader.php'));

        $content = file_get_contents(__DIR__ . '/../vendor/composer/autoload_psr4.php');
        $content = str_replace('$baseDir . \'/daux\'', 'PHAR_DIR . \'/daux\'', $content);
        $phar->addFromString('vendor/composer/autoload_psr4.php', $content);
    }

    private function addBinary($phar)
    {
        $content = file_get_contents(__DIR__ . '/../generate');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
        $phar->addFromString('generate', $content);
    }

    /**
     * Removes whitespace from a PHP source string while preserving line numbers.
     *
     * @param  string $source A PHP string
     * @return string The PHP string with the whitespace removed
     */
    private function stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }

        return $output;
    }

    private function getStub()
    {
        return <<<'EOF'
#!/usr/bin/env php
<?php
/*
 * This file is part of Daux.
 *
 * (c) Stéphane Goetz <onigoetz@onigoetz.ch>
 *
 * For the full copyright and license information, please view
 * the license that is located at the bottom of this file.
 */

define('PHAR_DIR', dirname(__FILE__));

Phar::mapPhar('daux.phar');

require 'phar://daux.phar/generate';

__HALT_COMPILER();
EOF;
    }
}
