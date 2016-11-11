<?php

namespace Lavary\Menus;

use Lavary\Menus\Common\Arr;
use Lavary\Menus\Common\Attr;
use Lavary\Menus\Matcher\MatcherInterface;
use Lavary\Menus\Configuration\Configuration;

class Manager
{
    /**
     * Menu configuration
     *
     * @var Lavary\Menus\Configuration\Configuration
     */
    protected $configuration;

    /**
     * Menu matcher
     *
     * @var Lavary\Menus\Matcher\MatcherInterface
     */
    protected $matcher;

    /**
     * The route group attribute stack.
     *
     * @var array
     */
    protected $groupStack = array();
    
    /**
    * The reserved attributes.
    *
    * @var array
    */
    protected $reserved = array('url', 'prefix', 'parent', 'raw', 'secure', 'id');

    /**
     * Generate an integer identifier for each new item
     *
     * @return int
     */
    public function __construct(Configuration $configuration, MatcherInterface $matcher)
    {
        $this->matcher       = $matcher;
        $this->configuration = $configuration;
    }

    /**
     * Sets the matcher object for this menu
     *
     * @param  \Lavary\Menus\Matcher\Matcher $matcher
     *
     * @return void
     */
    public function setMatcher(MatcherInterface $matcher)
    {
        $this->matcher = $matcher;
    }

    /**
     * Sets the configuration settings
     *
     * @param  \Lavary\Menus\Configuration\Configuration $configuration
     *
     * @return void
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function generateId()
    {
        return uniqid();
    }

    /**
     * Create a menu group with shared attributes.
     *
     * @param  array  $attributes
     * @param  callable  $closure
     *
     * @return void
     */
    public function group($attributes, $closure, Item $item)
    {
        $this->updateGroupStack($attributes);

        // Once we have updated the group stack, we will execute the user Closure and
        // merge in the groups attributes when the item is created. After we have
        // run the callback, we will pop the attributes off of this group stack.
        call_user_func($closure, $item);

        array_pop($this->groupStack);
    }

    /**
     * Update the group stack with the given attributes.
     *
     * @param  array  $attributes
     *
     * @return void
     */
    public function updateGroupStack(array $attributes = array())
    {
        if (count($this->groupStack) > 0) {
            $attributes = $this->mergeWithLastGroup($attributes);
        }

        $this->groupStack[] = $attributes;
    }

    /**
     * Merge the given array with the last group stack.
     *
     * @param  array  $new
     *
     * @return array
     */
    protected function mergeWithLastGroup($new)
    {
        return self::mergeGroup($new, end($this->groupStack));
    }

    /**
     * Merge the given group attributes.
     *
     * @param  array  $new
     * @param  array  $old
     *
     * @return array
     */
    protected static function mergeGroup($new, $old)
    {
        $new['prefix'] = self::formatGroupPrefix($old, $new);
        
        if (isset($old['class']) && isset($new['class'])) {
            $new['class']  = Attr::mergeClass(Arr::get($new, 'class'), Arr::get($old, 'class'));
            unset($old['class']);
        }

        return array_merge(Arr::except($old, array('prefix')), $new);
    }

    /**
     * Format the prefix for the new group attributes.
     *
     * @param  array  $old
     * @param  array  $new
     *
     * @return string
     */
    protected static function formatGroupPrefix($old, $new)
    {
        if (isset($new['prefix'])) {
            return trim(Arr::get($old, 'prefix'), '/') . '/' . trim($new['prefix'], '/');
        }
        
        return Arr::get($old, 'prefix');
    }

    /**
     * Get the prefix from the last group on the stack.
     *
     * @return string
     */
    public function getLastGroupPrefix()
    {
        if (count($this->groupStack) > 0) {
            return Arr::get(end($this->groupStack), 'prefix', '');
        }

        return null;
    }

    /**
     * Prefix the given URI with the last prefix.
     *
     * @param  string  $uri
     *
     * @return string
     */
    public function prefix($uri)
    {
        return trim(trim($this->getLastGroupPrefix(), '/') . '/'  . trim($uri, '/'), '/') ?: '/';
    }

    /**
     * Get the valid attributes from the options.
     *
     * @param  array   $options
     *
     * @return array
     */
    public function extractAttributes($options = array())
    {
        if (!is_array($options)) {
            $options = array();
        }
            
        if (count($this->groupStack) > 0) {
            $options = $this->mergeWithLastGroup($options);
        }

        return Arr::except($options, $this->reserved);
    }

    /**
     * Return configuration value by key
     *
     * @param string $key
     * @param string $default
     *
     * @return string
     */
    public function config($key, $default = null)
    {
        return $this->configuration->get($key, $default);
    }

    public function __get($prop)
    {
        if (property_exists($this, $prop)) {
            return $this->$prop;
        }

        throw new \BadMethodCallException();
    }
}
