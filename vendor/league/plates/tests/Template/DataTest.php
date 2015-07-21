<?php

namespace League\Plates\Template;

class DataTest extends \PHPUnit_Framework_TestCase
{
    private $template_data;

    public function setUp()
    {
        $this->template_data = new Data;
    }

    public function testCanCreateInstance()
    {
        $this->assertInstanceOf('League\Plates\Template\Data', $this->template_data);
    }

    public function testAddDataToAllTemplates()
    {
        $this->template_data->add(array('name' => 'Jonathan'));
        $data = $this->template_data->get();
        $this->assertEquals($data['name'], 'Jonathan');
    }

    public function testAddDataToOneTemplate()
    {
        $this->template_data->add(array('name' => 'Jonathan'), 'template');
        $data = $this->template_data->get('template');
        $this->assertEquals($data['name'], 'Jonathan');
    }

    public function testAddDataToOneTemplateAgain()
    {
        $this->template_data->add(array('firstname' => 'Jonathan'), 'template');
        $this->template_data->add(array('lastname' => 'Reinink'), 'template');
        $data = $this->template_data->get('template');
        $this->assertEquals($data['lastname'], 'Reinink');
    }

    public function testAddDataToSomeTemplates()
    {
        $this->template_data->add(array('name' => 'Jonathan'), array('template1', 'template2'));
        $data = $this->template_data->get('template1');
        $this->assertEquals($data['name'], 'Jonathan');
    }

    public function testAddDataWithInvalidTemplateFileType()
    {
        $this->setExpectedException('LogicException', 'The templates variable must be null, an array or a string, integer given.');
        $this->template_data->add(array('name' => 'Jonathan'), 123);
    }
}
