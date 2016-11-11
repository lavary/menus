<?php

namespace Lavary\Menus\Matcher;

use Lavary\Menus\Matcher\Pattern;

class Matcher implements MatcherInterface
{
    /**
     * @var \Lavary\Menus\Matcher\Pattern\PatternInterface
     */
    protected $patterns = [];

    /**
     * Add a pattern to the matcher
     *
     * @param \Lavary\Menus\Matcher\Pattern\PatternInterface $pattern
     *
     * @return $this
     */
    public function addPattern(Pattern\PatternInterface $pattern)
    {
        $this->patterns[] = $pattern;

        return $this;
    }

    /**
     * Return registered patterns
     *
     *
     * @return array
     */
    public function getPatterns()
    {
        return $this->patterns;
    }

    /**
     * Add an instance a regex pattern to the list of patterns
     *
     * @param \Lavary\Menus\Matcher\Pattern\PatternInterface $pattern
     *
     * @return $this
     */
    public function addRegex($regex)
    {
        $this->patterns[] = new Pattern\RegexPattern($regex);

        return $this;
    }

    /**
     * Check whether the item's URI is the current URI or not
     *
     * @param \Lavary\Menus\Item $item
     *
     * @return boolean
     */
    public function isCurrent(\Lavary\Menus\Item $item)
    {
        if ($item->isCurrent() === true) {
            return true;
        }

        foreach ($this->patterns as $pattern) {
            if ($pattern->match($item->getUri())) {
                return true;
            }
        }

        return false;
    }
}
