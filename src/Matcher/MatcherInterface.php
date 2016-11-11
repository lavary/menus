<?php

namespace Lavary\Menus\Matcher;

interface MatcherInterface
{
    /**
     * Make the item active
     *
     * @param \Lavary\Menus\Item $item
     *
     * @return boolean
     */
    public function isCurrent(\Lavary\Menus\Item $item);
}
