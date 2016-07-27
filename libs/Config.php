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

    public function getDocumentationDirectory() {
        return $this['docs_directory'];
    }

    public function setDocumentationDirectory($documentationPath) {
        $this['docs_directory'] = $documentationPath;
    }

    public function getThemesDirectory() {
        return $this['themes_directory'];
    }

    public function isMultilanguage() {
        return array_key_exists('languages', $this) && !empty($this['languages']);
    }
}
