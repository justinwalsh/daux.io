<?php

namespace League\Plates\Template;

use League\Plates\Engine;
use LogicException;

/**
 * Container which holds template data and provides access to template functions.
 */
class Template
{
    /**
     * Instance of the template engine.
     * @var Engine
     */
    protected $engine;

    /**
     * The name of the template.
     * @var Name
     */
    protected $name;

    /**
     * The data assigned to the template.
     * @var array
     */
    protected $data = array();

    /**
     * An array of section content.
     * @var array
     */
    protected $sections = array();

    /**
     * The name of the template layout.
     * @var string
     */
    protected $layoutName;

    /**
     * The data assigned to the template layout.
     * @var array
     */
    protected $layoutData;

    /**
     * Create new Template instance.
     * @param Engine $engine
     * @param string $name
     */
    public function __construct(Engine $engine, $name)
    {
        $this->engine = $engine;
        $this->name = new Name($engine, $name);

        $this->data($this->engine->getData($name));
    }

    /**
     * Magic method used to call extension functions.
     * @param  string $name
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->engine->getFunction($name)->call($this, $arguments);
    }

    /**
     * Assign data to template object.
     * @param  array $data
     * @return null
     */
    public function data(array $data)
    {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Check if the template exists.
     * @return boolean
     */
    public function exists()
    {
        return $this->name->doesPathExist();
    }

    /**
     * Get the template path.
     * @return string
     */
    public function path()
    {
        return $this->name->getPath();
    }

    /**
     * Render the template and layout.
     * @param  string $name
     * @param  array  $data
     * @return string
     */
    public function render(array $data = array())
    {
        try {

            $this->data($data);

            unset($data);

            extract($this->data);

            ob_start();

            if ($this->exists()) {

                include($this->path());

            } else {

                throw new LogicException(
                    'The template "' . $this->name->getName() . '" could not be found at "' . $this->path() . '".'
                );
            }

            $content = ob_get_clean();

            if (isset($this->layoutName)) {

                $layout = $this->engine->make($this->layoutName);
                $layout->sections = array_merge($this->sections, array('content' => $content));
                $content = $layout->render($this->layoutData);
            }

            return $content;

        } catch (LogicException $e) {

            ob_end_clean();

            throw new LogicException($e->getMessage());
        }
    }

    /**
     * Set the template's layout.
     * @param  string $name
     * @param  array  $data
     * @return null
     */
    protected function layout($name, array $data = array())
    {
        $this->layoutName = $name;
        $this->layoutData = $data;
    }

    /**
     * Start a new section block.
     * @param  string $name
     * @return null
     */
    protected function start($name)
    {
        if ($name === 'content') {

            throw new LogicException(
                'The section name "content" is reserved.'
            );
        }

        $this->sections[$name] = '';

        ob_start();
    }

    /**
     * Stop the current section block.
     * @return null
     */
    protected function stop()
    {
        if (empty($this->sections)) {

            throw new LogicException(
                'You must start a section before you can stop it.'
            );
        }

        end($this->sections);

        $this->sections[key($this->sections)] = ob_get_clean();
    }

    /**
     * Returns the content for a section block.
     * @param  string $name Section name
     * @param  string $default Default section content
     * @return string|null
     */
    protected function section($name, $default = null)
    {
        if (!isset($this->sections[$name])) {
            return $default;
        }

        return $this->sections[$name];
    }

    /**
     * Fetch a rendered template.
     * @param  string $name
     * @param  array  $data
     * @return string
     */
    protected function fetch($name, array $data = array())
    {
        return $this->engine->render($name, $data);
    }

    /**
     * Output a rendered template.
     * @param  string $name
     * @param  array  $data
     * @return null
     */
    protected function insert($name, array $data = array())
    {
        echo $this->engine->render($name, $data);
    }

    /**
     * Apply multiple functions to variable.
     * @param  mixed  $var
     * @param  string $functions
     * @return mixed
     */
    protected function batch($var, $functions)
    {
        foreach (explode('|', $functions) as $function) {

            if ($this->engine->doesFunctionExist($function)) {

                $var = call_user_func(array($this, $function), $var);

            } elseif (is_callable($function)) {

                $var = call_user_func($function, $var);

            } else {

                throw new LogicException(
                    'The batch function could not find the "' . $function . '" function.'
                );
            }
        }

        return $var;
    }

    /**
     * Escape string.
     * @param  string      $string
     * @param  null|string $functions
     * @return string
     */
    protected function escape($string, $functions = null)
    {
        if ($functions) {
            $string = $this->batch($string, $functions);
        }

        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Alias to escape function.
     * @param  string      $string
     * @param  null|string $functions
     * @return string
     */
    protected function e($string, $functions = null)
    {
        return $this->escape($string, $functions);
    }
}

if (!defined('ENT_SUBSTITUTE')) {
    define('ENT_SUBSTITUTE', 8);
}
