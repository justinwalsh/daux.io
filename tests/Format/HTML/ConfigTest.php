<?php namespace Todaymade\Daux\Format\HTML;

use Todaymade\Daux\Config as MainConfig;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    function testHTMLConfigCreation() {
        $config = new MainConfig(['html' => ['edit_on' => 'test']]);

        $this->assertInstanceOf(Config::class, $config->getHTML());
        $this->assertEquals('test', $config->getHTML()['edit_on']);
    }

    public function providerEditOn()
    {
        $github_result = ['name' => 'GitHub', 'basepath' => 'https://github.com/justinwalsh/daux.io/blob/master/docs'];

        return [
            [[], null],
            [['edit_on_github' => 'justinwalsh/daux.io/blob/master/docs'], $github_result],

            // Allow formatting in many ways
            [['edit_on_github' => 'justinwalsh/daux.io/blob/master/docs/'], $github_result],
            [['edit_on_github' => '/justinwalsh/daux.io/blob/master/docs'], $github_result],
            [['edit_on_github' => 'https://github.com/justinwalsh/daux.io/blob/master/docs/'], $github_result],
            [['edit_on_github' => 'http://github.com/justinwalsh/daux.io/blob/master/docs/'], $github_result],

            // Fallback if a string is provided to 'edit_on'
            [['edit_on' => 'justinwalsh/daux.io/blob/master/docs'], $github_result],

            // Support any provider
            [
                ['edit_on' => ['name' => 'Bitbucket', 'basepath' => 'https://bitbucket.org/onigoetz/daux.io/src/master/docs/']],
                ['name' => 'Bitbucket', 'basepath' => 'https://bitbucket.org/onigoetz/daux.io/src/master/docs']
            ]
        ];
    }

    /**
     * @dataProvider providerEditOn
     */
    public function testEditOn($value, $expected)
    {
        $config = new Config($value);

        $this->assertEquals($expected, $config->getEditOn());
    }
}
