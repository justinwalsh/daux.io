<?php namespace Todaymade\Daux;

use ArrayObject;
use Todaymade\Daux\Tree\Content;

class Config extends ArrayObject
{
    /**
     * Merge an array into the object
     *
     * @param array $newValues
     * @param bool $override
     */
    public function merge($newValues, $override = true)
    {
        foreach ($newValues as $key => $value) {
            // If the key doesn't exist yet,
            // we can simply set it.
            if (!array_key_exists($key, $this)) {
                $this[$key] = $value;
                continue;
            }

            // We already know this value exists
            // so if we're in conservative mode
            // we can skip this key
            if ($override === false) {
                continue;
            }

            // Merge the values only if
            // both values are arrays
            if (is_array($this[$key]) && is_array($value)) {
                $this[$key] = array_replace_recursive($this[$key], $value);
            } else {
                $this[$key] = $value;
            }
        }
    }

    /**
     * Merge an array into the object, ignore already added keys.
     *
     * @param $newValues
     */
    public function conservativeMerge($newValues)
    {
        $this->merge($newValues, false);
    }

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

    public function setFormat($format)
    {
        $this['format'] = $format;
    }

    public function getFormat()
    {
        return $this['format'];
    }

    public function isMultilanguage()
    {
        return array_key_exists('languages', $this) && !empty($this['languages']);
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
}
