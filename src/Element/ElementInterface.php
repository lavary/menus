<?php

namespace Lavary\Menu\Element;

interface ElementInterface
{
    /**
     * Render the collection
     *
     * @param \Lavary\Menu\Collection $collection
     *
     * @return string
     */
    public function render(\Lavary\Menu\Collection $collection);
}
