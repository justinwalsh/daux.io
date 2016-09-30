<?php namespace Todaymade\Daux;

use Todaymade\Daux\Tree\Content;
use Todaymade\Daux\Format\HTML\Config as HTMLConfig;

class Config extends BaseConfig
{
    public function getCurrentPage()
    {
        return $this['current_page'];
    }

    public function setCurrentPage(Content $entry)
    {
        $this['current_page'] = $entry;
    }

    public function getDocumentationDirectory()
    {
        return $this['docs_directory'];
    }

    public function setDocumentationDirectory($documentationPath)
    {
        $this['docs_directory'] = $documentationPath;
    }

    public function getThemesDirectory()
    {
        return $this['themes_directory'];
    }

    public function setThemesDirectory($directory)
    {
        $this['themes_directory'] = $directory;
    }

    public function setThemesPath($themePath)
    {
        $this['themes_path'] = $themePath;
    }

    public function getThemesPath()
    {
        return $this['themes_path'];
    }

    public function setFormat($format)
    {
        $this['format'] = $format;
    }

    public function getFormat()
    {
        return $this['format'];
    }

    public function hasTimezone()
    {
        return isset($this['timezone']);
    }

    public function getTimezone()
    {
        return $this['timezone'];
    }

    public function isMultilanguage()
    {
        return array_key_exists('languages', $this) && !empty($this['languages']);
    }

    public function isLive()
    {
        return $this['mode'] == Daux::LIVE_MODE;
    }

    public function isStatic()
    {
        return $this['mode'] == Daux::STATIC_MODE;
    }

    public function shouldInheritIndex()
    {
        // As the global configuration is always present, we
        // need to test for the existence of the legacy value
        // first. Then use the current value.
        if (array_key_exists('live', $this) && array_key_exists('inherit_index', $this['live'])) {
            return $this['live']['inherit_index'];
        }

        return $this['html']['inherit_index'];
    }

    public function setConfigurationOverrideFile($override_file)
    {
        $this['override_file'] = $override_file;
    }

    public function getConfigurationOverrideFile()
    {
        if (array_key_exists('override_file', $this)) {
            return $this['override_file'];
        }

        return null;
    }

    public function getConfluenceConfiguration()
    {
        return $this['confluence'];
    }

    public function getHTML()
    {
        return new HTMLConfig($this['html']);
    }
}
