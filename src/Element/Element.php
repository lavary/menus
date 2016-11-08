<?php

namespace Lavary\Menu\Element;

use Lavary\Menu\Common\Attr;
use Lavary\Menu\Collection;

abstract class Element
{
    /**
     * Default hooks to be executed before an item is rendered
     *
     * @var array
     */
    protected $defaultHooks = [
        'addCurrentUriAttributes',
        // ...
    ];

    /**
     * Generate the menu items as list items using a recursive function
     *
     * @param \Lavary\Menu\Collection
     * @param string $type
     *
     * @return string
     */
    protected function populate(Collection $collection, $type = 'ul', array $dropdown = [])
    {
        $items = '';
        $tag = $this->getTag($type);
        
        foreach ($collection as $item) {
            $this->runHooks($item);
            
            $items .= $this->openTag($tag, $item->attr());
            $items .= $this->addTagText($item);

            // Rendering sub-items
            if ($item->hasChildren()) {
                $items .= $this->openTag($type, $dropdown, static::populate($item->getChildren(), $type, $dropdown));
            }
                            
            $items .= $this->closeTag($tag);

            if ($divider = $item->getDivider()) {
                $items .= $this->openTag($tag, $divider) . $this->closeTag($tag);
            }
        }
        
        return $items;
    }
    
    /**
     * Return HTML link if item has a link
     *
     * @param \Lavary\Menu\Item $item
     *
     * @return string
     */
    protected function addTagText(\Lavary\Menu\Item $item)
    {
        if ($link = $item->getLink()) {
            $link->attr('href', $item->getUri());
            return $this->openTag('a', $link->attr(), $item->getTitle());
        } else {
            return $item->getTitle();
        }
    }

    /**
     * Open an HTML tag
     *
     * @param string $tag
     * @param array $properties
     * @param string $content
     *
     * @return string
     */
    protected function openTag($tag, $properties, $content = null)
    {
        $el = '<' . $tag . Attr::printAttributes($properties) . '>';

        if (! is_null($content)) {
            $el .= $content . "</$tag>";
        }

        return $el;
    }

    /**
     * Open an HTML tag
     *
     * @param string $tag
     * @param array $properties
     * @param string $content
     *
     * @return string
     */
    protected function closeTag($tag)
    {
        return "</$tag>";
    }

    /**
     * Return the proper HTML tag according to the passed type
     *
     * @param string $type
     *
     * @return string
     */
    protected function getTag($type)
    {
        return in_array($type, array('ul', 'ol')) ? 'li' : $type;
    }

    /**
     * Add current URI attributes to the item - if it's the current URI
     *
     * @return VOID
     */
    protected function addCurrentUriAttributes($item)
    {
        if ($item->isCurrent()) {
            $item->addCurrentUriAttributes();
        }
    }

    /**
     * Run the registered hooks
     *
     * @param Lavary\Menu\Item $item
     *
     * @return void
     */
    protected function runHooks(\Lavary\Menu\Item $item)
    {
        $hooks = array_merge($this->defaultHooks, $this->hooks());
        foreach ($hooks as $hook) {
            $hook = str_replace('()', '', $hook);
            if (method_exists($this, $hook)) {
                $this->$hook($item);
            }
        }
    }

    /**
     * List of the hooks to be executed before an item is rendered
     *
     * @return array
     */
    protected function hooks()
    {
        return [];
    }
}
