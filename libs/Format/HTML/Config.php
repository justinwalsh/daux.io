<?php namespace Todaymade\Daux\Format\HTML;

use Todaymade\Daux\BaseConfig;

class Config extends BaseConfig
{
    private function prepareGithubUrl($url)
    {
        $url = str_replace('http://', 'https://', $url);

        return [
            'name' => 'GitHub',
            'basepath' => (strpos($url, 'https://github.com/') === 0 ? '' : 'https://github.com/') . trim($url, '/')
        ];
    }

    function getEditOn()
    {
        if (array_key_exists('edit_on', $this)) {
            if (is_string($this['edit_on'])) {
                return $this->prepareGithubUrl($this['edit_on']);
            } else {

                $this['edit_on']['basepath'] = rtrim($this['edit_on']['basepath'], '/');

                return $this['edit_on'];
            }
        }

        if (array_key_exists('edit_on_github', $this)) {
            return $this->prepareGithubUrl($this['edit_on_github']);
        }

        return null;
    }
}
