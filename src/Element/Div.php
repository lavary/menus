<?php

namespace Lavary\Menu\Element;

use Lavary\Menu\Common\Attr;

class Div extends Element implements ElementInterface
{
    /**
     * Stores attributes of the <ul> tag
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Stores attributes of the inner <ul> tags
     *
     * @var array
     */
    protected $dropdown = [];

    /**
     * Instantiates the formatter
     *
     */
    public function __construct(array $attributes = [], array $dropdown = [])
    {
        $this->attributes = $attributes;
        $this->dropdown   = $dropdown;
    }

    /**
     * Returns the menu as an unordered list.
     *
     * @param \Lavary\Menu\Collection $collection
     *
     * @return string
     */
    public function render(\Lavary\Menu\Collection $collection)
    {
        return '<div' . Attr::printAttributes($this->attributes) . '>' . static::populate($collection, 'div', $this->dropdown) . '</div>';
    }
}
