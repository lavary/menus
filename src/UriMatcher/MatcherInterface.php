<?php

namespace Lavary\Menus\UriMatcher;

interface MatcherInterface
{
    /**
     * Make the item active
     *
     * @param \Lavary\Menus\Item $item
     *
     * @return boolean
     */
    public function isCurrentUri(\Lavary\Menus\Item $item);
}
