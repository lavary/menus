<?php

namespace Lavary\Menus\UriMatcher\Pattern;

class RegexPattern implements PatternInterface
{
    /**
     * The current URI which is passed to the object
     *
     * @var string $uri
     */
    protected $regex;

    public function __construct($regex = null)
    {
        $this->regex = $regex;
    }

    /**
     * Define whether the item'S URI matches the current URI
     *
     * @param \Lavary\Menus\Item $item
     *
     * @return boolean
     */
    public function match($uri)
    {
        if (preg_match($this->regex, $uri)) {
            return true;
        }

        return false;
    }
}
