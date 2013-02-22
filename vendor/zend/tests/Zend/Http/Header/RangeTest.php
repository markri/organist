<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Range;

class RangeTest extends \PHPUnit_Framework_TestCase
{

    public function testRangeFromStringCreatesValidRangeHeader()
    {
        $rangeHeader = Range::fromString('Range: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $rangeHeader);
        $this->assertInstanceOf('Zend\Http\Header\Range', $rangeHeader);
    }

    public function testRangeGetFieldNameReturnsHeaderName()
    {
        $rangeHeader = new Range();
        $this->assertEquals('Range', $rangeHeader->getFieldName());
    }

    public function testRangeGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Range needs to be completed');

        $rangeHeader = new Range();
        $this->assertEquals('xxx', $rangeHeader->getFieldValue());
    }

    public function testRangeToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Range needs to be completed');

        $rangeHeader = new Range();

        // @todo set some values, then test output
        $this->assertEmpty('Range: xxx', $rangeHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

