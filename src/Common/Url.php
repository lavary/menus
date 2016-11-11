<?php

namespace Lavary\Menus\Common;

class Url
{
    /**
    * Valid URL components
    *
    * @var array
    */
    protected static $parts = [PHP_URL_SCHEME, PHP_URL_HOST, PHP_URL_PORT, PHP_URL_USER, PHP_URL_PASS, PHP_URL_PATH, PHP_URL_QUERY, PHP_URL_FRAGMENT];

    /**
     * Returns the URL path
     *
     * @param  string  $path
     *
     * @return string
     */
    public static function path()
    {
        return static::parse(PHP_URL_PATH);
    }

    /**
     * Returns the URL path
     *
     * @param  string  $path
     *
     * @return string
     */
    public static function url()
    {
        return static::getRequestUri();
    }

    /**
     * Returns the URL path
     *
     * @param  string  $path
     *
     * @return string
     */
    public static function host()
    {
        return static::getHttpHost();
    }

    /**
     * Parse the current URi
     *
     * @param  string  $path
     *
     * @return string
     */
    public static function parse($part)
    {
        if (!isset(static::$parts[$part])) {
            $part = PHP_URL_PATH;
        }

        return parse_url(static::getRequestUri(), $part);
    }

    /**
     * Determine if the given path is a valid URL.
     *
     * @param  string  $path
     *
     * @return bool
     */
    public function isValidUrl($path)
    {
        if (Str::startsWith($path, ['#', '//', 'mailto:', 'tel:', 'http://', 'https://'])) {
            return true;
        }

        return filter_var($path, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Get the scheme for a raw URL.
     *
     * @param  bool|null  $secure
     *
     * @return string
     */
    public function getScheme($secure = false)
    {
        return $secure ? 'https://' : 'http://';
    }

    /**
     * Force the passed schema to the URL
     *
     * @param  bool   $scheme
     * @param  string $path
     *
     * @return string
     */
    public function forceScheme($scheme, $url)
    {
        $start = Str::startsWith($url, 'http://') ? 'http://' : 'https://';
        
        return preg_replace('~' . $start . '~', $scheme, $url, 1);
    }

    /**
     * Get current request's URI
     *
     * @return string
     */
    protected static function getRequestUri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Get current request's URI
     *
     * @return string
     */
    protected static function getHttpHost()
    {
        return ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http')
        . '://'
        . $_SERVER['HTTP_HOST'];
    }
}
