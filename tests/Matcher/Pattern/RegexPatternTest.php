<?php

namespace Lavary\Menus\Tests;

use Lavary\Menus\Matcher\Pattern\RegexPattern;
use Lavary\Menus\Item;

class RegexPatternTest extends \PHPUnit_Framework_TestCase
{
   /**
    * The pattern instance
    *
    * @var \Lavary\Menus\Matcher\Pattern\UriPattern
    */
    protected $pattern;

    public function setUp()
    {
        $this->pattern = new RegexPattern('/\/some\/dummy\/url\/to\/resource(.*)/');
    }
    
    /**
     * @dataProvider uriProvider
     */
    public function testMatch($match, $url)
    {
        $this->assertSame($match, $this->pattern->match($url));
    }

    public function uriProvider()
    {
        return [
            [true,  '/some/dummy/url/to/resource'],
            [true,  '/some/dummy/url/to/resource/'],
            [true,  '/some/dummy/url/to/resource/index.php'],
            [true,  '/some/dummy/url/to/resource/?id=250'],
            [true,  '/some/dummy/url/to/resource/entity/12/edit'],
            [false, null],
            [false, true],
            [false, 'some/url'],
            [false, 'some/dummy/url/to/resource'],
        ];
    }
}
