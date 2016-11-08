<?php

namespace Lavary\Menu\Common;

class Helpers
{
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}
