<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Functional;

use League\CommonMark\CommonMarkConverter;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Tests the parser against locally-stored examples
 *
 * This is particularly useful for testing minor variations allowed by the spec
 * or small regressions not tested by the spec.
 */
class LocalDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CommonMarkConverter
     */
    protected $converter;

    protected function setUp()
    {
        $this->converter = new CommonMarkConverter();
    }

    /**
     * @param string $markdown Markdown to parse
     * @param string $html     Expected result
     * @param string $testName Name of the test
     *
     * @dataProvider dataProvider
     */
    public function testExample($markdown, $html, $testName)
    {
        $actualResult = $this->converter->convertToHtml($markdown);

        $failureMessage = sprintf('Unexpected result for "%s" test', $testName);
        $failureMessage .= "\n=== markdown ===============\n" . $markdown;
        $failureMessage .= "\n=== expected ===============\n" . $html;
        $failureMessage .= "\n=== got ====================\n" . $actualResult;

        $this->assertEquals($html, $actualResult, $failureMessage);
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        $finder = new Finder();
        $finder->files()
            ->in(__DIR__.'/data')
            ->name('*.md');

        $ret = [];

        /** @var SplFileInfo $markdownFile */
        foreach ($finder as $markdownFile) {
            $testName = $markdownFile->getBasename('.md');
            $markdown = $markdownFile->getContents();
            $html = file_get_contents(__DIR__.'/data/'.$testName.'.html');

            $ret[] = [$markdown, $html, $testName];
        }

        return $ret;
    }
}
