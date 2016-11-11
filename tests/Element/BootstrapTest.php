<?php

namespace Lavary\Menus\Tests;

use Lavary\Menus\Manager;
use Lavary\Menus\Item;
use Lavary\Menus\Element\Bootstrap;

class BootstrapTest extends \PHPUnit_Framework_TestCase
{
   /**
    * Menu builder instance
    *
    * @var \Lavary\Menus\Manager
    */
    protected $manager;

    /**
     * Item instance
     *
     * @var \Lavary\Menus\Item
     */
    protected $menu;

    /**
     * Bootstrap renderer
     *
     * @var \Lavary\Menus\Element\Bootstrap
     */
    protected $bootstrap;

    public function setUp()
    {
        $this->manager = $this->getMockBuilder('Lavary\Menus\Manager')
                              ->disableOriginalConstructor()
                              ->getMock();
        
        $this->manager->method('extractAttributes')->will($this->returnValue(['class' => 'foo bar']));

        $this->menu = new Item('Title', [], $this->manager);
        $this->bootstrap = new Bootstrap();
    }

    public function testDisableRaw()
    {
        $item = $this->menu->add('Raw Item', ['raw' => true]);
        $this->invoke($this->bootstrap, 'disableRaw', [$item]);
        $this->assertEquals('foo bar disabled', $item->attr('class'));
    }

    public function testAddCaret()
    {
        $item = $this->menu->add('Item');
        $item->add('Child');

        $this->invoke($this->bootstrap, 'addCaret', [$item]);
        $this->assertEquals('Item <span class="caret"></span>', $item->getTitle());
    }

    public function testAddBootstrapStyles()
    {
        $item = $this->menu->add('Item');
        $item->add('Child');

        $this->invoke($this->bootstrap, 'addBootstrapStyles', [$item]);
        
        $this->assertEquals('foo bar dropdown', $item->attr('class'));
        $this->assertEquals(
            ['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'role' => 'button', 'aria-haspopup' => 'true', 'aria-expanded' => 'false'],
            $item->getLink()->attr()
        );
    }

    protected function invoke(&$object, $method, array $args = [])
    {
        $ref    = new \ReflectionClass(get_class($object));
        $method = $ref->getMethod($method);
        
        $method->setAccessible(true);

        return $method->invokeArgs($object, $args);
    }
}
