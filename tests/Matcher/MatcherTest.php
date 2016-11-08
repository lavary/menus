<?php

namespace Lavary\Menu\Tests;

use Lavary\Menu\Matcher\Matcher;
use Lavary\Menu\Matcher\Pattern\PatternInterface;
use Lavary\Menu\Matcher\Pattern\RegexPattern;
use Lavary\Menu\Item;

class MatcherTest extends \PHPUnit_Framework_TestCase
{
   /**
    * The matcher instance
    *
    * @var \Lavary\Menu\Matcher\Matcher
    */
    protected $matcher;

    public function setUp()
    {
        $this->matcher = new Matcher();
    }

    public function testAddPattern()
    {
        $pattern = $this->getMock(PatternInterface::class);

        $this->matcher->addPattern($pattern);
        $this->matcher->addPattern($pattern);
        $this->matcher->addPattern($pattern);

        $patterns = $this->matcher->getPatterns();
        $this->assertCount(3, $patterns);
        $this->assertContainsOnlyInstancesOf(PatternInterface::class, $patterns);
    }

    public function testAddRegex()
    {
        $this->matcher->addRegex('/some/url/w+/i');
        $this->matcher->addRegex('/some/url/d+/i');

        $patterns = $this->matcher->getPatterns();
        $this->assertCount(2, $patterns);
        $this->assertContainsOnlyInstancesOf(RegexPattern::class, $patterns);
    }

    public function testIsCurrentWithoutPatterns()
    {
        $item = $this->getMock(Item::class);
        $this->assertFalse($this->matcher->isCurrent($item));
        $item->method('isCurrent')->will($this->returnValue(true));
        $this->assertTrue($this->matcher->isCurrent($item));
    }

    public function testIsCurrentWithPatternsSuccess()
    {
        $item = $this->getMock(Item::class);
        $patterns = [
            $this->getMock(PatternInterface::class),
            $this->getMock(PatternInterface::class),
        ];
        
        $patterns[0]->method('match')->will($this->returnValue(true));
        
        $this->matcher->addPattern($patterns[0])
                      ->addPattern($patterns[1]);

        $this->assertTrue($this->matcher->isCurrent($item));
    }

    public function testIsCurrentWithPatternFail()
    {
        $item = $this->getMock(Item::class);
        $patterns = [
            $this->getMock(PatternInterface::class),
            $this->getMock(PatternInterface::class),
        ];

         $this->matcher->addPattern($patterns[0])
                       ->addpattern($patterns[1]);

        $this->assertFalse($this->matcher->isCurrent($item));
    }
}
