<?php namespace Todaymade\Daux\Tree;


class BuilderTest extends \PHPUnit_Framework_TestCase
{
    public function providerRemoveSorting()
    {
        return array(
            ['01_before', 'before'],
            ['-Down', 'Down'],
            ["01_numeric", 'numeric'],
            ["01_A_File", "A_File"],
            ["A_File", "A_File"],
            ["01_Continuing", "Continuing"],
            ["-01_Coming", "Coming"],
            ["-02_Soon", "Soon"],
            ["01_Getting_Started", "Getting_Started"],
            ["API_Calls", "API_Calls"],
            ["200_Something_Else-Cool", "Something_Else-Cool"],
            ["_5_Ways_to_Be_Happy", "5_Ways_to_Be_Happy"],
            ["Before_but_after", "Before_but_after"],
            ["Continuing", "Continuing"],
            ["01_GitHub_Flavored_Markdown", "GitHub_Flavored_Markdown"],
            ["Code_Test", "Code_Test"],
            ["05_Code_Highlighting", "Code_Highlighting"],
            ["1", "1"],
        );
    }

    /**
     * @dataProvider providerRemoveSorting
     */
    public function testRemoveSorting($value, $expected)
    {
        $this->assertEquals($expected, Builder::removeSortingInformations($value));
    }
}
