<?php

namespace Lavary\Menus\Common;

class Attr
{
    /**
     * Build an HTML attribute string from an array.
     *
     * @return string
     */
    public static function printAttributes(array $attributes)
    {
        $html = array();
        foreach ($attributes as $key => $value) {
            $element = self::attributeElement($key, $value);
            if (! is_null($element)) {
                $html[] = $element;
            }
        }
        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }
    
    /**
     * Build a single attribute element.
     *
     * @param  string  $key
     * @param  string  $value
     *
     * @return string
     */
    protected static function attributeElement($key, $value)
    {
        if (is_numeric($key)) {
            $key = $value;
        }
        if (! is_null($value)) {
            return $key . '="' . htmlentities($value, ENT_QUOTES, 'UTF-8', false) . '"';
        }
    }

    /**
     * Get the valid attributes from the options.
     *
     * @param  array   $options
     *
     * @return string
     */
    public static function mergeClass($old, $new)
    {
        $classes = (trim($old) ? trim($old) . ' ' : '') . trim($new);
        
        return implode(' ', array_unique(explode(' ', $classes)));
    }

    /**
     * Merge passed attributes
     *
     * @param  array $itemContainer
     * @param  array $anchorLink
     * @param  array $dropdownMenu
     *
     * @return $this
     */
    public static function mergeAttributes(array $old = [], array $new = [])
    {
        if (isset($old['class']) && isset($new['class'])) {
            $new['class'] = static::mergeClass($old['class'], $new['class']);
            unset($old['class']);
        }
        
        return array_merge($old, $new);
    }
}
