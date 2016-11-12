<?php

namespace Lavary\Menus\Tests;

use Lavary\Menus\UriMatcher\Matcher;
use Lavary\Menus\UriMatcher\Pattern\PatternInterface;
use Lavary\Menus\UriMatcher\Pattern\RegexPattern;
use Lavary\Menus\Item;

class MatcherTest extends \PHPUnit_Framework_TestCase
{
   /**
    * The matcher instance
    *
    * @var \Lavary\Menus\UriMatcher\Matcher
    */
    protected $matcher;

    public function setUp()
    {
        $this->matcher = new Matcher();
    }

    public function testAddPattern()
    {
        $pattern = $this->getMockBuilder('\Lavary\Menus\UriMatcher\Pattern\PatternInterface')
                        ->getMock();

        $this->matcher->addPattern($pattern);
        $this->matcher->addPattern($pattern);
        $this->matcher->addPattern($pattern);

        $patterns = $this->matcher->getPatterns();
        $this->assertCount(3, $patterns);
        $this->assertContainsOnlyInstancesOf('\Lavary\Menus\UriMatcher\Pattern\PatternInterface', $patterns);
    }

    public function testAddRegex()
    {
        $this->matcher->addRegex('/some/url/w+/i');
        $this->matcher->addRegex('/some/url/d+/i');

        $patterns = $this->matcher->getPatterns();
        $this->assertCount(2, $patterns);
        $this->assertContainsOnlyInstancesOf('\Lavary\Menus\UriMatcher\Pattern\RegexPattern', $patterns);
    }

    public function testIsCurrentUriWithoutPatterns()
    {
        $item = $this->getMockBuilder('\Lavary\Menus\Item')
                     ->disableOriginalConstructor()
                     ->getMock();
    
        $this->assertFalse($this->matcher->isCurrentUri($item));
        $item->method('isCurrent')->will($this->returnValue(true));
        $this->assertTrue($this->matcher->isCurrentUri($item));
    }

    public function testIsCurrentUriWithPatternsSuccess()
    {
        $item = $this->getMockBuilder('\Lavary\Menus\Item')
                     ->disableOriginalConstructor()
                     ->getMock();
        
        $patterns = [
            $this->getMockBuilder('\Lavary\Menus\UriMatcher\Pattern\PatternInterface')->getMock(),
            $this->getMockBuilder('\Lavary\Menus\UriMatcher\Pattern\PatternInterface')->getMock(),
        ];
        
        $patterns[0]->method('match')->will($this->returnValue(true));
        
        $this->matcher->addPattern($patterns[0])
                      ->addPattern($patterns[1]);

        $this->assertTrue($this->matcher->isCurrentUri($item));
    }

    public function testIsCurrentWithPatternFail()
    {
        $item = $this->getMockBuilder('\Lavary\Menus\Item')
                     ->disableOriginalConstructor()
                     ->getMock();
        
        $patterns = [
            $this->getMockBuilder('\Lavary\Menus\UriMatcher\Pattern\PatternInterface')->getMock(),
            $this->getMockBuilder('\Lavary\Menus\UriMatcher\Pattern\PatternInterface')->getMock(),
        ];

         $this->matcher->addPattern($patterns[0])
                       ->addpattern($patterns[1]);

        $this->assertFalse($this->matcher->isCurrentUri($item));
    }
}
