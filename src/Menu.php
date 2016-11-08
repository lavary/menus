<?php

namespace Lavary\Menu;

use Lavary\Menu\Matcher\Matcher;
use Lavary\Menu\Matcher\MatcherInterface;
use Lavary\Menu\Configuration\Configuration;

class Menu
{
    /**
     * Menu collection
     *
     * @var Lavary\Menu\Collection
     */
    protected $collection;

    /**
     * Menu manager
     *
     * @var Lavary\Menu\Manager
     */
    protected $manager;
 
    /**
     * Initializing the menu builder
     *
     */
    public function __construct(Configuration $configuration = null)
    {
        $this->collection = new Collection();
        
        if (is_null($configuration)) {
            $configuration = new Configuration();
        }

        // Matcher wit ha default pattern
        $matcher = new Matcher();
        $matcher->addPattern(new \Lavary\Menu\Matcher\Pattern\UriPattern($_SERVER['REQUEST_URI']));

        $this->manager = new Manager($configuration, $matcher);
    }

    /**
     * Create a new menu
     *
     * @param  string  $name
     * @param  callable  $callback
     *
     * @return \Lavary\Menu\Item
     */
    public function make($name, \Closure $callback)
    {
        if (is_callable($callback)) {
            $menu = new Item($name, [], $this->manager);
           
            // Registering the items
            call_user_func($callback, $menu);
            
            // Storing each menu instance in the collection
            $this->collection->put($name, $menu);
            
            return $menu;
        }
    }

    /**
     * Sets the matcher object for this menu
     *
     * @param  \Lavary\Menu\Matcher\Matcher $matcher
     *
     * @return void
     */
    public function setMatcher(MatcherInterface $matcher)
    {
        $this->manager->setMatcher($matcher);
    }

    /**
     * Sets the configuration settings
     *
     * @param  \Lavary\Menu\Configuration\Configuration $configuration
     *
     * @return void
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->manager->setConfiguration($configuration);
    }

    /**
     * Get a menu by its title
     *
     * @param  string $title
     *
     * @return void
     */
    public function get($title)
    {
        return isset($this->collection[$title]) ? $this->collection[$title] : null;
    }
}
