<?php namespace Todaymade\Daux\Tree;

use Todaymade\Daux\Config;

class DirectoryTest extends \PHPUnit_Framework_TestCase {

    public function providerSort()
    {
        return array(
            array(["005_Fifth", "01_First"], ["01_First", "005_Fifth"]),
            array(["005_Fifth", "Another", "01_First"], ["01_First", "005_Fifth", "Another"]),
            array(["005_Fifth", "Another", "-Sticky", "01_First"], ["01_First", "005_Fifth", "Another", "-Sticky"]),
            array(['01_before', '-Down'], ['01_before', '-Down']),
            array(['01_before', '-Down-after', '-Down'], ['01_before', '-Down', '-Down-after']),
            array(["01_numeric", "01_before"], ["01_before", "01_numeric"]),
            array(["A_File", "01_A_File"], ["01_A_File", "A_File"]),
            array(["A_File", "01_Continuing", "-01_Coming", "-02_Soon"], ["01_Continuing", "A_File", "-01_Coming", "-02_Soon"]),
            array(["01_Getting_Started", "API_Calls", "200_Something_Else-Cool", "_5_Ways_to_Be_Happy"], ["01_Getting_Started", "200_Something_Else-Cool", "_5_Ways_to_Be_Happy", "API_Calls"]),
            array(["01_Getting_Started", "API_Calls", "index", "200_Something_Else-Cool", "_5_Ways_to_Be_Happy"], ["index", "01_Getting_Started", "200_Something_Else-Cool", "_5_Ways_to_Be_Happy", "API_Calls"]),
            array(["Before_but_after", "A_File", "Continuing"], ["A_File", "Before_but_after", "Continuing"]),
            array(["01_GitHub_Flavored_Markdown", "Code_Test", "05_Code_Highlighting"], ["01_GitHub_Flavored_Markdown", "05_Code_Highlighting", "Code_Test"]),
        );
    }

    /**
     * @dataProvider providerSort
     */
    public function testSort($list, $expected)
    {
        shuffle($list);
        $directory = new Directory(new Root(new Config(), ''), 'dir');

        foreach ($list as $value) {
            $entry = new Content($directory, $value);
            $entry->setName($value);
        }

        $directory->sort();

        $final = [];
        foreach ($directory->getEntries() as $obj) {
            $final[] = $obj->getName();
        }

        $this->assertEquals($expected, $final);
    }


}
