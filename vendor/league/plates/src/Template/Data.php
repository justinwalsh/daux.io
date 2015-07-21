<?php

namespace League\Plates\Template;

use LogicException;

/**
 * Preassigned template data.
 */
class Data
{
    /**
     * Variables shared by all templates.
     * @var array
     */
    protected $sharedVariables = array();

    /**
     * Specific template variables.
     * @var array
     */
    protected $templateVariables = array();

    /**
     * Add template data.
     * @param  array             $data;
     * @param  null|string|array $templates;
     * @return Data
     */
    public function add(array $data, $templates = null)
    {
        if (is_null($templates)) {
            return $this->shareWithAll($data);
        } elseif (is_array($templates)) {
            return $this->shareWithSome($data, $templates);
        } elseif (is_string($templates)) {
            return $this->shareWithSome($data, array($templates));
        } else {
            throw new LogicException(
                'The templates variable must be null, an array or a string, ' . gettype($templates) . ' given.'
            );
        }
    }

    /**
     * Add data shared with all templates.
     * @param  array $data;
     * @return Data
     */
    public function shareWithAll($data)
    {
        $this->sharedVariables = array_merge($this->sharedVariables, $data);

        return $this;
    }

    /**
     * Add data shared with some templates.
     * @param  array $data;
     * @param  array $templates;
     * @return Data
     */
    public function shareWithSome($data, array $templates)
    {
        foreach ($templates as $template) {
            if (isset($this->templateVariables[$template])) {
                $this->templateVariables[$template] = array_merge($this->templateVariables[$template], $data);
            } else {
                $this->templateVariables[$template] = $data;
            }
        }

        return $this;
    }

    /**
     * Get template data.
     * @param  null|string $template;
     * @return array
     */
    public function get($template = null)
    {
        if (!is_null($template) and isset($this->templateVariables[$template])) {
            return array_merge($this->sharedVariables, $this->templateVariables[$template]);
        } else {
            return $this->sharedVariables;
        }
    }
}
