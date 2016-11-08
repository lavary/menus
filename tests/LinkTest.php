<?php

namespace Lavary\Menu\Tests;

use Lavary\Menu\Link;

class LinkTest extends \PHPUnit_Framework_TestCase
{
   /**
    * Link instance
    *
    * @var \Lavary\Menu\Link
    */
    protected $link;

    public function setUp()
    {
        $this->link = new Link(['url' => 'dummy/path', 'prefix' => 'some/prefix']);
    }

    public function tearDown()
    {
        // Pass
    }

    public function testAttr()
    {
        $this->link->attr('key', 'value');
        $this->assertEquals('value', $this->link->attr('key'));

        $this->link->attr(['role' => 'button', 'data-toggle' => 'dropdown']);
        $this->assertCount(2, $this->link->attr());
        $this->assertNull($this->link->attr('key'));
        $this->assertEquals('dropdown', $this->link->attr('data-toggle'));
    
        $this->link->attr('data-collapse', true);
        $this->assertSame(['role' => 'button', 'data-toggle' => 'dropdown', 'data-collapse' => true], $this->link->attr());
    }

    public function testHref()
    {
        $this->link->setHref('something');
        $this->assertEquals('something', $this->link->getHref());
    }

    public function testGetPatInfo()
    {
        $path = $this->link->getPathInfo();
        $this->assertEquals('array', gettype($path));
        $this->assertArrayHasKey('url', $path);
        $this->assertArrayHasKey('prefix', $path);
    }
}
