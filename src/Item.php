<?php

namespace Lavary\Menus;

use Lavary\Menus\Common\Str;
use Lavary\Menus\Common\Arr;
use Lavary\Menus\Common\Url;
use Lavary\Menus\Common\Attr;
use Lavary\Menus\Traits\Printer;
use Lavary\Menus\Renderer\Element;

class Item implements Attributable
{
    use Printer;
    
    /**
     * Reference to the menu manager
     *
     * @var Lavary\Menus\Manager
     */
    protected $manager;

    /**
     * The ID of the menu item
     *
     * @var int
     */
    protected $id;

    /**
     * Item's title
     *
     * @var string
     */
    protected $title;

    /**
     * Item's title in camelCase
     *
     * @var string
     */
    protected $nickname;

    /**
     * Item's seprator from the rest of the items, if it has any.
     *
     * @var array
     */
    protected $divider = [];

    /**
     * Parent Id of the menu item
     *
     * @var int
     */
    protected $parent = null;

    /**
     * Children collection
     *
     * @var \Lavary\Menus\Collection
     */
    protected $children = null;
    
    /**
     * Extra information attached to the menu item
     *
     * @var array
     */
    protected $data = [];
    
    /**
     * Attributes of menu item
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Item's anchor object
     *
     * @var Lavary\Menus\Link
     */
    protected $link = null;

    /**
     * Defines whether the item is active (current URI) or not
     *
     * @var array
     */
    protected $current = null;

    /**
     * Creates a new Lavary\Menus\MenuItem instance.
     *
     * @param  string  $title
     * @param  string  $url
     * @param  array  $attributes
     * @param  int  $parent
     * @param  Lavary\Menus\Manager $manager
     *
     * @return void
     */
    public function __construct($title, array $options, Manager $manager)
    {
        $this->id         = Arr::get($options, 'id');
        $this->parent     = Arr::get($options, 'parent');
        $this->manager    = $manager;
        $this->title      = $title;
        $this->nickname   = isset($options['nickname']) ? $options['nickname'] : Str::camel(Str::ascii($title));
        $this->attributes = $this->manager->extractAttributes($options);
        
        $path = $this->populatePath($options);
        
        $this->link     = $path ? new Link($path) : null;
        $this->children = new Collection();

        // Activate the item if items's url matches the request uri
        if (true === $this->manager->config('auto_activate')) {
            if ($this->manager->matcher->isCurrentUri($this)) {
                $this->setCurrent(true);
            }
        }
    }

    /**
     * Return path info to create a link object
     *
     * @param array $options
     *
     * @return array
     */
    public function populatePath($options)
    {
        $path = [];
        
        if (isset($options['raw']) && $options['raw'] == true) {
            return null;
        }

        if (! is_array($options)) {
            $path = array('url' => $options);
        } else {
            $path = Arr::only($options, array('url', 'secure'));
        }

        $path['prefix'] = $this->manager->getLastGroupPrefix();
        
        return $path;
    }

    /**
     * Return item's link
     *
     * @return Lavary\Menus\Link
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Return item's parent object
     *
     * @return Lavary\Menus\Item
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Return item's manager
     *
     * @return Lavary\Menus\Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Adds sub-item
     *
     * @param  string  $title
     * @param  string|array  $options
     *
     * @return Lavary\Menus\Item $item
     */
    public function add($title, $options = null)
    {
        $item = new Item($title, $this->prepareOptions($options), $this->manager);
        
        $this->children[$title] = $item;
        
        return $item;
    }

    /**
     * Add additional data to the options array
     *
     * @param string|array
     *
     * @return array
     */
    public function prepareOptions($options)
    {
        if (! is_array($options)) {
            $options = ['url' => $options];
        }

        $options['id'] = isset($options['id']) ? isset($options['id'])  : $this->manager->generateId();
        $options['parent'] = $this;

        return $options;
    }

    /**
     * Add a plain text item
     *
     * @param string $title
     * @param array $options
     *
     * @return Lavary\Menus\Item
     */
    public function raw($title, array $options = [])
    {
        $options['raw'] = true;
        
        return $this->add($title, $options);
    }

    /**
     * Insert a seprator after the item
     *
     * @param array $attributes
     *
     * @return void
     */
    public function divide($attributes = [])
    {
        $attributes['class'] = Attr::mergeClass(Arr::get($attributes, 'class'), 'divider');
        $this->divider = $attributes;

        return $this;
    }

    /**
     * Insert a seprator after the item
     *
     * @return $this
     */
    public function addCurrentUriAttributes()
    {
        $attributes = $this->manager->config('active_attributes');
        
        if ($this->manager->config('active_element') == 'item') {
            $this->attributes = Attr::mergeAttributes($this->attributes, $attributes);
        } else {
            $this->link->attr(Attr::mergeAttributes($this->link->attr(), $attributes));
        }

        return $this;
    }

    /**
     * Group children of the item
     *
     * @param  array $attributes
     * @param  callable $closure
     *
     * @return void
     */
    public function group($attributes, $closure)
    {
        $this->manager->group($attributes, $closure, $this);
    }

    /**
     * Add attributes to the item
     *
     * @param  mixed
     *
     * @return mixed
     */
    public function attr()
    {
        $args = func_get_args();

        if (isset($args[0]) && is_array($args[0])) {
            $this->attributes = $args[0];
            return $this;
        } elseif (isset($args[0]) && isset($args[1])) {
            $this->attributes[$args[0]] = $args[1];
            return $this;
        } elseif (isset($args[0])) {
            return isset($this->attributes[$args[0]]) ? $this->attributes[$args[0]] : null;
        }

        return $this->attributes;
    }

    /**
     * Add a html class to the item's classes
     *
     * @param string
     *
     * @return Lavary\Menus\Item
     */
    public function addClass($class)
    {
        $this->attributes['class'] = Attr::mergeClass(Arr::get($this->attributes, 'class'), $class);

        return $this;
    }

    /**
     * Generate URL for link
     *
     * @return string
     */
    public function getUri()
    {
        // If the item has a link proceed:
        if (! is_null($this->link)) {
            // If item's link has `href` property explcitly defined
            // return it
            if ($this->link->getHref()) {
                return $this->link->getHref();
            }
            // Otherwise dispatch to the proper address
            return $this->populateUri($this->link->getPathInfo());
        }
    }

    /**
     * Get the action for a "url" option.
     *
     * @param  array|string  $options
     *
     * @return string
     */
    protected function populateUri($options)
    {
        $url = null;

        if (isset($options['secure'])) {
            $url = Url::forceScheme(Url::getScheme(Arr::get($options, 'secure')), $site_url);
        }

        if (Arr::get($options, 'prefix')) {
            $url .= '/' . trim($options['prefix'], '/');
        }

        if (Arr::get($options, 'url')) {
            $url .=  '/' . trim($options['url'], '/');
        }

        return $url;
    }

    /**
     * Prepends text or html to the item
     *
     * @return Lavary\Menus\Item
     */
    public function prependText($html)
    {
        $this->title = $html . $this->title;
    
        return $this;
    }

    /**
     * Appends text or html to the item
     *
     * @return Lavary\Menus\Item
     */
    public function appendText($html)
    {
        $this->title .= $html;
        
        return $this;
    }

    /**
     * Checks if the item has any children
     *
     * @return boolean
     */
    public function hasChildren()
    {
        return count($this->children) or false;
    }

    /**
     * Returns item's children
     *
     * @return Lavary\Menus\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

     /**
     * Set item's title
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Returns item's title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Return item's divider
     *
     * @return array
     */
    public function getDivider()
    {
        return $this->divider;
    }

    /**
     * Set item's Id
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Return Item's Id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set or get items's meta data
     *
     * @param array $args
     * @param bool $cascade
     *
     * @return array
     */
    public function setData(array $args = [], $cascade = false)
    {
        if (isset($args[0]) && is_array($args[0])) {
            $this->data = array_change_key_case($args[0]);
            
            // Cascade data to item's children if cascade_data option is enabled
            if (true === $cascade) {
                foreach ($this->children as $item) {
                    $item->setData($args, true);
                }
            }

            return $this;
        } elseif (isset($args[0]) && isset($args[1])) {
            $this->data[$args[0]] = $args[1];
            
            // Cascade data to item's children if cascade_data option is enabled
            if (true === $cascade) {
                foreach ($this->children as $item) {
                    $item->setData($args, true);
                }
            }

            return $this;
        }
    }

    /**
     * Set or get items's meta data
     *
     * @param  mixed
     *
     * @return string|Lavary\Menus\Item
     */
    public function data()
    {
        $args = func_get_args();
        
        if (! count($args)) {
            return $this->data;
        }

        if (count($args) == 1 && ! is_array($args[0])) {
            return isset($this->data[$args[0]]) ? $this->data[$args[0]] : null;
        } else {
            return $this->setData($args, $this->manager->config('cascade_data'));
        }
    }

    /**
     * Render the menu using a certain element
     *
     * @param \Lavary\Menus\Renderer\Element $element
     *
     * @return string
     */
    public function render(Element $element)
    {
        return $element->render($this->children);
    }

    /**
     * Set the item's status
     *
     * @param boolean $status
     *
     * @return $this
     */
    public function forceCurrentStatus($status)
    {
        $this->current = $status;
        
        return $this;
    }
    
    /**
     * Set the item's status with bubbling
     *
     * @param boolean $status
     *
     * @return $this
     */
    public function setCurrent($status)
    {
        $this->current = true;
        if (! is_null($this->getParent()) && $this->getManager()->config('current_affect_parents')) {
            $this->currentBubble($status, $this->getParent());
        }

        return $this;
    }

    /**
     * Set the item's parent status recursively
     *
     * @param boolean $status
     *
     */
    protected function currentBubble($status, \Lavary\Menus\Item $item = null)
    {
        $item->forceCurrentStatus($status);
        $parent = $item->getParent();
        
        if ($parent && $item->getId()) {
            $this->currentBubble($status, $parent);
        }
    }

    /**
     * Define whether the item is active
     *
     * @return boolean
     */
    public function isCurrent()
    {
        return $this->current or false;
    }

    /**
     * Filter menu items by user callbacks
     *
     * @param  callable $callback
     *
     * @return Lavary\Menus\Builder
     */
    public function filter()
    {
        $args = func_get_args();
        
        if (! count($args)) {
            throw new \BadMethodCallException('Method filter() should have at least one parameter; None given.');
        }

        if (is_callable($args[0])) {
            $this->children = $this->children->filter($args[0]);
        } else {
            $key   = $args[0];
            $value = isset($args[1]) ? $args[1] : true;
            if (isset($args[1]) && $args[1] == true) {
                $this->children = $this->recursiveFilter($args[0], $value);
            } else {
                $this->children = $this->children->filter(function ($item) use ($key, $value) {
                    return $item->data($key) == $value;
                });
            }
        }
    }

    /**
     * Sorts the menu based on user's callable
     *
     * @param string $sort_by
     * @param string|callable $sort_type
     *
     * @return Lavary\Menus\Builder
     */
    public function sortBy($sort_by, $sort_type = 'asc')
    {
        if (is_callable($sort_by)) {
            $rslt = call_user_func($sort_by, $this->children->toArray());

            if (! is_array($rslt)) {
                $rslt = array($rslt);
            }

            $this->children = new Collection($rslt);

            return $this;
        }
        
        // running the sort proccess on the sortable items
        $this->children = $this->children->sort(function ($f, $s) use ($sort_by, $sort_type) {
            
            $f = $f->data($sort_by);
            $s = $s->data($sort_by);
            
            if ($f == $s) {
                return 0;
            }

            if ($sort_type == 'asc') {
                return $f > $s ? 1 : -1;
            }
            
            return $f < $s ? 1 : -1;
        });

        // Sorting the children recursively
        foreach ($this->children as $id => $item) {
            if ($item->getParent()) {
                $item->sortBy($sort_by, $sort_type);
            }
        }

        return $this;
    }

    /**
     * Returns menu item by name
     *
     * @return Lavary\Menus\Item
     */
    public function get($title)
    {
        return $this->whereNickname($title)
                    ->first();
    }

    /**
     * Returns menu item by Id
     *
     * @return Lavary\Menus\Item
     */
    public function find($id)
    {
        return $this->whereId($id)
                    ->first();
    }

    /**
     * Check if propery exists either in the class or the meta collection
     *
     * @param  String  $property
     *
     * @return Boolean
     */
    public function hasProperty($property)
    {
        if (property_exists($this, $property) || ! is_null($this->data($property))) {
            return true;
        }

        return false;
    }

    /**
     * Filter items recursively
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return Lavary\Menus\Collection
     */
    protected function recursiveFilter($attribute, $value)
    {
        $collection = new Collection;
        
        // Iterate over all the items in the main collection
        $this->children->each(function ($item) use ($attribute, $value, &$collection) {
            
            $attribute_value = property_exists($item, $attribute) ? $item->attribute : $item->data($attribute);
            if ($attribute_value == $value) {
                $collection->push($item);
                
                // Check if item has any children
                if ($item->children) {
                    $item->children = $item->recursiveFilter($attribute, $value);
                }
            }
        });

        return $collection;
    }


    /**
     * Search the menu based on an attribute
     *
     * @param string $method
     * @param array  $args
     *
     * @return Lavary\Menus\Item
     */
    public function __call($method, $args)
    {
        preg_match('/^[W|w]here([a-zA-Z0-9_]+)$/', $method, $matches);
        
        if ($matches) {
            $attribute = strtolower($matches[1]);
        } else {
            throw new \BadMethodCallException();
        }

        $value = $args ? $args[0] : null;
        $recursive = isset($args[1]) ? $args[1] : false;

        if ($recursive) {
            return $this->recursiveFilter($attribute, $value);
        }

        return $this->children->filter(function ($item) use ($attribute, $value) {
            if (!$item->hasProperty($attribute)) {
                return false;
            }
            
            if ($item->$attribute == $value) {
                return true;
            }
                        
            return false;
        })
        ->values();
    }

    /**
     * Return inaccessible properties or item's meta data
     *
     * @param  string
     *
     * @return string
     */
    public function __get($prop)
    {
        
        return $this->whereNickname($prop)
                    ->first();
    }
}
