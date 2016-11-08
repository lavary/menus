<?php

namespace Lavary\Menu\Tests;

use Lavary\Menu\Manager;
use Lavary\Menu\Item;

class ItemTest extends \PHPUnit_Framework_TestCase
{

   /**
    * Menu builder instance
    *
    * @var \Lavary\Menu\Manager
    */
    protected $manager;

    /**
    * Item instance
    *
    * @var \Lavary\Menu\Item
    */
    protected $menu;

    public function setUp()
    {
        $this->manager = $this->createMock(Manager::class);
        $this->menu = new Item('Title', [], $this->manager);
    }

    public function tearDown()
    {
        // Pass
    }

    public function testGetOption()
    {
        $path = $this->menu->populatePath(['raw' => true]);
        $this->assertNull($path);

        $path = $this->menu->populatePath('test-url');
        $this->assertArrayHasKey('url', $path);
        $this->assertEquals('test-url', $path['url']);

        $path = $this->menu->populatePath(['url' => 'any-url', 'secure' => true, 'foor' => 'bar']);
        $this->assertArrayHasKey('url', $path);
        $this->assertArrayHasKey('secure', $path);
        $this->assertArrayHasKey('prefix', $path);
        $this->assertArrayNotHasKey('foo', $path);
    }

    public function testGetLink()
    {
        $this->assertInstanceOf(\Lavary\Menu\Link::class, $this->menu->getLink());
    }

    public function testGetParent()
    {
        $child = $this->menu->add('test');
        $this->assertInstanceOf(\Lavary\Menu\Item::class, $child->getParent());
    }

    public function testGetManager()
    {
        $this->assertInstanceOf(\Lavary\Menu\Manager::class, $this->menu->getManager());
    }

    public function testPrepareOptions()
    {
        $options = $this->menu->prepareOptions('test-url');

        $this->assertArrayHasKey('url', $options);
        $this->assertEquals('test-url', $options['url']);
        $this->assertInstanceOf(Item::class, $options['parent']);
        $this->assertNull($options['id']);

        $options = $this->menu->prepareOptions(['id' => 15]);
        $this->assertEquals(15, $options['id']);

        $options = $this->menu->prepareOptions(['id' => null]);
        $this->assertNull($options['id']);
    }

    public function testAdd()
    {
        $child = $this->menu->add('First Item');

        $this->assertInstanceOf(Item::class, $child);
        
        $child = $this->menu->add('Second Item');
        
        $this->assertCount(2, $this->menu->getChildren());
    }

    public function testRaw()
    {
        $this->assertNull($this->menu->add('Item', ['raw' => true])->getLink());
    }

    public function testDivider()
    {
        $item = $this->menu->add('Item')->divide(['data-role' => 'divider', 'class' => 'test-1 test-2']);
        
        $this->assertNotNull($item->getDivider());
        $this->assertArrayHasKey('data-role', $item->getDivider());
        $this->assertEquals('test-1 test-2 divider', $item->getDivider()['class']);
    }

    public function testAttr()
    {
        $this->menu->attr('key', 'value');
        $this->assertEquals('value', $this->menu->attr('key'));

        $this->menu->attr(['role' => 'button', 'data-toggle' => 'dropdown']);
        $this->assertCount(2, $this->menu->attr());
        $this->assertNull($this->menu->attr('key'));
        $this->assertEquals('dropdown', $this->menu->attr('data-toggle'));
        
        $this->menu->attr('data-collapse', true);
        $this->assertSame(['role' => 'button', 'data-toggle' => 'dropdown', 'data-collapse' => true], $this->menu->attr());
    }

    public function testPrependText()
    {

        $this->menu->prependText('<span class="glyphicon glyphicon-envelope"></span> ');
        $this->assertEquals('<span class="glyphicon glyphicon-envelope"></span> Title', $this->menu->getTitle());
    }

    public function testAppendText()
    {
        $this->menu->appendText(' <span class="caret"></span>');
        $this->assertEquals('Title <span class="caret"></span>', $this->menu->getTitle());
    }

    public function testTitle()
    {
        $this->assertEquals('Title', $this->menu->getTitle());
        $this->menu->setTitle('Another Title');
        $this->assertEquals('Another Title', $this->menu->getTitle());
    }

    public function testId()
    {
        $this->assertNull($this->menu->getId());
        $this->menu->setId(145);
        $this->assertEquals(145, $this->menu->getId());
    }

    public function testHasProperty()
    {
        $this->assertFalse($this->menu->hasProperty('dummyProperty'));
        $this->menu->data('dummyProperty', true);
        $this->assertCount(1, $this->menu->data());
        $this->assertTrue($this->menu->data('dummyProperty'));
    }

    public function testData()
    {
        $this->assertCount(0, $this->menu->data());
        
        $this->menu->data('show', true);
        $this->assertCount(1, $this->menu->data());
        $this->assertTrue($this->menu->data('show'));

        $this->menu->data(['foo' => 'bar', 'foobar' => true]);
        $this->assertCount(2, $this->menu->data());
        $this->assertEquals('bar', $this->menu->data('foo'));
        $this->assertTrue($this->menu->data('foobar'));
        
        $this->menu->data('another', true);
        $this->assertCount(3, $this->menu->data());
        $this->assertTrue($this->menu->data('another'));
    }

    public function testSetData()
    {
        $this->menu->add('first')
                   ->add('second')
                   ->add('third');

        $this->menu->setData(['key', 'testValue'], true);

        $this->assertEquals('testValue', $this->menu->first->data('key'));
        $this->assertEquals('testValue', $this->menu->first->second->data('key'));
        $this->assertEquals('testValue', $this->menu->first->second->third->data('key'));
    }

    public function testHasChildren()
    {
        $this->assertFalse($this->menu->hasChildren());

        $this->menu->add('First');
        $this->menu->add('Second');
        $this->menu->add('Third');

        $this->assertTrue($this->menu->hasChildren());
    }

    public function testGetChildren()
    {
        $this->assertCount(0, $this->menu->getChildren());

        $this->menu->add('First');
        $this->menu->add('Second');
        $this->menu->add('Third');

        $this->assertCount(3, $this->menu->getChildren());
        $this->assertContainsOnlyInstancesOf(Item::class, $this->menu->getChildren());
    }

    public function testAddClass()
    {
        $item = $this->menu->add('Title');
        $item->addClass('foo bar');
        $item->addClass('another class');
        
        $this->assertEquals('foo bar another class', $item->attr('class'));
    }

    
    public function testGetUri()
    {
        $item = $this->menu->add('First Item', 'item/url');
        $this->assertEquals('/item/url', $item->getUri());

        $item->getLink()->setHref('url/for/href');
        $this->assertEquals('url/for/href', $item->getUri());

        $this->manager->expects($this->any())
        ->method('getLastGroupPrefix')
        ->will($this->returnValue('test/prefix'));

        $item = $this->menu->add('Second Item', 'another/item-url');
        $this->assertEquals('/test/prefix/another/item-url', $item->getUri());
    }

    public function testRender()
    {
        $el = $this->createMock(\Lavary\Menu\Element\Div::class);
        $el->expects($this->once())->method('render');

        $this->menu->render($el);
    }

    public function testForceCurrentStatus()
    {
        $item = $this->menu->add('Item');
        $this->assertFalse($item->isCurrent());

        $item->forceCurrentStatus(true);
        $this->assertTrue($item->isCurrent());
    }
   
    public function testSetCurrent()
    {
        $parent = $this->menu->add('Item');
        $child  = $parent->add('Child');
        
        $this->assertFalse($parent->isCurrent());
        $this->assertFalse($child->isCurrent());

        $child->setCurrent(true);
        $this->assertFalse($parent->isCurrent());
        $this->assertTrue($child->isCurrent());

        $this->manager->expects($this->once())->method('config')->will($this->returnValue(true));

        $child->setCurrent(true);
        $this->assertTrue($parent->isCurrent());
        $this->assertTrue($child->isCurrent());
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testFilterFail()
    {
        $this->menu->filter();
    }

    public function testFilterCallback()
    {
        $this->menu->add('Home')->data('show', true);
        $this->menu->add('About')->data('show', true);
        $this->menu->add('Contact')->data('show', false);
        
        $this->menu->filter(function ($item) {
            return $item->data('show') === true;
        });

        $this->AssertCount(2, $this->menu->getChildren());
    }

    public function testFilter()
    {
        $home    = $this->menu->add('Home')->data('show', true);
        $about   = $this->menu->add('About')->data('show', true);
        $contact = $this->menu->add('Contact')->data('show', false);

        $about->add('History');
        $about->add('Goals');
        $contact->add('Address');

        $this->AssertCount(3, $this->menu->getChildren());
        $this->menu->filter('show');
        $this->AssertCount(2, $this->menu->getChildren());
        $this->assertCount(2, $about->getChildren());
        $this->assertCount(1, $contact->getChildren());
    }

    public function testFilterRecursive()
    {
        $home    = $this->menu->add('Home')->data('show', true);
        $about   = $this->menu->add('About')->data('show', true);
        $contact = $this->menu->add('Contact')->data('show', false);
        
        $about->add('History')->data('show', true);
        $about->add('What we do')->data('show', true);
        $about->add('Goals');
        
        $contact->add('Address')->data('show', true);
        
        $this->menu->filter('show', true, true);
        
        $this->AssertCount(2, $this->menu->getChildren());
        $this->AssertCount(2, $about->getChildren());
        $this->AssertCount(1, $contact->getChildren());
    }

    public function testFilterReturnedInstance()
    {
        $about   = $this->menu->add('About')->data('show', true);
    
        $about->add('History')->data('show', true);
        $about->add('What we do')->data('show', true);
        $about->add('Goals');
                
        $this->menu->filter('show', true, true);
        
        $this->assertContainsOnlyInstancesOf(Item::class, $this->menu->getChildren());
        $this->assertContainsOnlyInstancesOf(Item::class, $about->getChildren());
    }

    public function testSortByAsc()
    {
        $home    = $this->menu->add('History')->data('order', 10);
        $about   = $this->menu->add('What we do')->data('order', 8);
        $contact = $this->menu->add('Goals')->data('order', 50);

        $about->add('History')->data('order', 5);
        $about->add('What we do')->data('order', 12);
        $about->add('Goals')->data('order', 2);

        $this->menu->sortBy('order');

        $this->assertEquals(8, $this->menu->getChildren()->first()->data('order'));
        $this->assertEquals(50, $this->menu->getChildren()->last()->data('order'));
        $this->assertEquals(2, $about->getChildren()->first()->data('order'));
        $this->assertEquals(12, $about->getChildren()->last()->data('order'));
    }

    public function testSortDesc()
    {
        $home    = $this->menu->add('History')->data('order', 10);
        $about   = $this->menu->add('What we do')->data('order', 8);
        $contact = $this->menu->add('Goals')->data('order', 50);

        $about->add('History')->data('order', 5);
        $about->add('What we do')->data('order', 12);
        $about->add('Goals')->data('order', 2);

        $this->menu->sortBy('order', 'desc');

        $this->assertEquals(50, $this->menu->getChildren()->first()->data('order'));
        $this->assertEquals(8, $this->menu->getChildren()->last()->data('order'));
        $this->assertEquals(12, $about->getChildren()->first()->data('order'));
        $this->assertEquals(2, $about->getChildren()->last()->data('order'));
    }

    public function testGet()
    {
        $item = $this->menu->add('What we do');
        $fetched = $this->menu->get('whatWeDo');

        $this->assertInstanceOf(Item::class, $fetched);
        $this->assertEquals('What we do', $fetched->getTitle());
    }


    public function testFind()
    {
        $item = $this->menu->add('What we do');
        $fetched = $this->menu->find($item->getId());

        $this->assertInstanceOf(Item::class, $fetched);
        $this->assertEquals('What we do', $fetched->getTitle());
    }
}
