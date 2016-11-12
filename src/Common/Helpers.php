<?php

namespace Lavary\Menus\Common;

/**
 * Hepler methods
 *
 * This methods have been adapted from illuminate/support package
 *
 * @package  illuminate/support
 * @author   Taylor Otwell <taylorotwell@gmail.com>
 */
class Helpers
{
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public static function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}
