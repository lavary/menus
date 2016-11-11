<?php

namespace Lavary\Menus;

class Link implements Attributable
{
    /**
     * Path Information
     *
     * @var array
     */
    protected $path = array();

    /**
     * Explicit href for the link
     *
     * @var string
     */
    protected $href;

    /**
     * Link attributes
     *
     * @var array
     */
    protected $attributes = [];
    
    /**
     * Creates a hyper link instance
     *
     * @param  array  $path
     *
     * @return void
     */
    public function __construct($path = array())
    {
        $this->path = $path;
    }

    /**
     * Return the link's path
     *
     * @return array
     */
    public function getPathInfo()
    {
        return $this->path;
    }

    /**
     * return anchor's href property
     *
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * Set anchor's href property
     *
     * @param string $href
     *
     * @return Lavary\Menus\Link
     */
    public function setHref($href)
    {
    
        $this->href = $href;
        
        return $this;
    }

    /**
     * Add attributes to the item
     *
     * @param  mixed
     *
     * @return string|array
     */
    public function attr()
    {
        $args = func_get_args();

        if (isset($args[0]) && is_array($args[0])) {
            $this->attributes = $args[0];
            return $this;
        } elseif (isset($args[0]) && isset($args[1])) {
            $this->attributes[$args[0]] = $args[1];
            return $this;
        } elseif (isset($args[0])) {
            return isset($this->attributes[$args[0]]) ? $this->attributes[$args[0]] : null;
        }

        return $this->attributes;
    }
}
