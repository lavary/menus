<?php

namespace Lavary\Menus\UriMatcher\Pattern;

interface PatternInterface
{
    /**
     * Define whether the item'S URI matches the current URI
     *
     * @param string $uri
     *
     * @return boolean
     */
    public function match($uri);
}
