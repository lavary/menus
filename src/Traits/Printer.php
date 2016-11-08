<?php

namespace Lavary\Menu\Traits;

use Lavary\Menu\Element\Ul;
use Lavary\Menu\Element\Ol;
use Lavary\Menu\Element\Div;
use Lavary\Menu\Element\Bootstrap;

trait Printer
{
    /**
     * Returns the menu as an unordered list
     *
     * @param array $attributes
     * @param array $innerDropdown
     *
     * @return string
     */
    public function asUl(array $attributes = [], array $innerDropdown = [])
    {
        return $this->render(new Ul($attributes, $innerDropdown));
    }

    /**
     * Returns the menu as an ordered list
     *
     * @param array $attributes
     * @param array $innerDropdown
     *
     * @return string
     */
    public function asOl(array $attributes = [], array $innerDropdown = [])
    {
        return $this->render(new Ol($attributes, $innerDropdown));
    }

    /**
     * Returns the menu as division tags
     *
     * @param array $attributes
     * @param array $innerDropdown
     *
     * @return string
     */
    public function asDiv(array $attributes = [], array $innerDropdown = [])
    {
        return $this->render(new Div($attributes, $innerDropdown));
    }

    /**
     * Returns the menu in a Bootstrap friendly format
     *
     * @param array $attributes
     * @param array $innerDropdown
     *
     * @return string
     */
    public function asBootstrap(array $attributes = [], array $innerDropdown = [])
    {
        return $this->render(new Bootstrap($attributes, $innerDropdown));
    }
}
