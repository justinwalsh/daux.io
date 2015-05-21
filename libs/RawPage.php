<?php namespace Todaymade\Daux;

use Todaymade\Daux\Server\MimeType;

class RawPage implements Page
{

    private $file;

    public function __construct($filename)
    {
        $this->file = $filename;
    }

    public function getContent()
    {
        throw new Exception("you should not use this method to show a raw content");
    }

    public function display()
    {
        header('Content-type: ' . MimeType::get($this->file));
        header('Content-length: ' . filesize($this->file));

        // Transfer file in 1024 byte chunks to save memory usage.
        if ($fd = fopen($this->file, 'rb')) {
            while (!feof($fd)) {
                print fread($fd, 1024);
            }
            fclose($fd);
        }
    }
}
