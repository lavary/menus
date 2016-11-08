<?php

namespace Lavary\Menu\Matcher\Pattern;

class UriPattern implements PatternInterface
{
    /**
     * The current URI which is passed to the object
     *
     * @var string $uri
     */
    protected $uri;

    public function __construct($uri = null)
    {
        $this->uri = $this->normalizeUri($uri);
    }

    /**
     * Define whether the item'S URI matches the current URI
     *
     * @param \Lavary\Menu\Item $item
     *
     * @return boolean
     */
    public function match($uri)
    {
        return $this->uri == $this->normalizeUri($uri);
    }

    protected function normalizeUri($url)
    {
        return trim(str_replace('/index.php', '', $url), '/');
    }
}
