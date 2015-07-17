<?php namespace Todaymade\Daux;

use ArrayObject;

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
            if ($override === false && array_key_exists($key, $this)) {
                continue;
            }

            $this[$key] = $value;
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
}
