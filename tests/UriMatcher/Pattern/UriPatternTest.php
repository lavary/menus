<?php

namespace Lavary\Menus\Tests;

use Lavary\Menus\UriMatcher\Pattern\UriPattern;
use Lavary\Menus\Item;

class UriPatternTest extends \PHPUnit_Framework_TestCase
{
   /**
    * The pattern instance
    *
    * @var \Lavary\Menus\UriMatcher\Pattern\UriPattern
    */
    protected $pattern;

    public function setUp()
    {
        $this->pattern = new UriPattern('/some/dummy/url');
    }

    public function testMatchFail()
    {
        $this->assertFalse($this->pattern->match('/some/dummmy/url'));
        $this->assertFalse($this->pattern->match('/some/dummmy/url/index.php'));
    }

    public function testMatchSuccess()
    {
        $this->assertTrue($this->pattern->match('some/dummy/url/'));
        $this->assertTrue($this->pattern->match('some/dummy/url/'));
        $this->assertTrue($this->pattern->match('some/dummy/url/index.php'));
    }
}
