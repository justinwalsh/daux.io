<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Functional;

use League\CommonMark\CommonMarkConverter;

/**
 * Tests the parser against the CommonMark spec
 */
class SpecTest extends \PHPUnit_Framework_TestCase
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
     * @param string $section  Section of the spec
     * @param int    $number   Example number
     *
     * @dataProvider dataProvider
     */
    public function testExample($markdown, $html, $section, $number)
    {
        $actualResult = $this->converter->convertToHtml($markdown);

        $failureMessage = sprintf('Unexpected result ("%s" section, example #%d)', $section, $number);
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
        $filename = __DIR__ . '/../../vendor/jgm/CommonMark/spec.txt';
        if (($data = file_get_contents($filename)) === false) {
            $this->fail(sprintf('Failed to load spec from %s', $filename));
        }

        $matches = array();
        // Normalize newlines for platform independence
        $data = preg_replace('/\r\n?/', "\n", $data);
        $data = preg_replace('/<!-- END TESTS -->.*$/', '', $data);
        preg_match_all('/^\.\n([\s\S]*?)^\.\n([\s\S]*?)^\.$|^#{1,6} *(.*)$/m', $data, $matches, PREG_SET_ORDER);

        $examples = array();
        $currentSection = "";
        $exampleNumber = 0;

        foreach ($matches as $match) {
            if (isset($match[3])) {
                $currentSection = $match[3];
            } else {
                $exampleNumber++;

                $markdown = $match[1];
                $markdown = str_replace('→', "\t", $markdown);

                $examples[] = array(
                    'markdown' => $markdown,
                    'html' => $match[2],
                    'section' => $currentSection,
                    'number' => $exampleNumber
                );
            }
        }

        return $examples;
    }
}
