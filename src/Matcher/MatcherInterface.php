<?php

namespace Lavary\Menu\Matcher;

interface MatcherInterface
{
    /**
     * Make the item active
     *
     * @param \Lavary\Menu\Item $item
     *
     * @return boolean
     */
    public function isCurrent(\Lavary\Menu\Item $item);
}
