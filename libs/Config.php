<?php namespace Todaymade\Daux;

use ArrayObject;

class Config extends ArrayObject {

    public function merge($newValues, $override = true) {
        foreach ($newValues as $key => $value) {
            if (array_key_exists($key, $this) && $override == false) {
                continue;
            }

            $this[$key] = $value;
        }
    }

    public function conservativeMerge($newValues) {
        $this->merge($newValues, false);
    }
}
