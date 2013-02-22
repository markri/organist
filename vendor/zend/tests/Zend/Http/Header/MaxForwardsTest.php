<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\MaxForwards;

class MaxForwardsTest extends \PHPUnit_Framework_TestCase
{

    public function testMaxForwardsFromStringCreatesValidMaxForwardsHeader()
    {
        $maxForwardsHeader = MaxForwards::fromString('Max-Forwards: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $maxForwardsHeader);
        $this->assertInstanceOf('Zend\Http\Header\MaxForwards', $maxForwardsHeader);
    }

    public function testMaxForwardsGetFieldNameReturnsHeaderName()
    {
        $maxForwardsHeader = new MaxForwards();
        $this->assertEquals('Max-Forwards', $maxForwardsHeader->getFieldName());
    }

    public function testMaxForwardsGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('MaxForwards needs to be completed');

        $maxForwardsHeader = new MaxForwards();
        $this->assertEquals('xxx', $maxForwardsHeader->getFieldValue());
    }

    public function testMaxForwardsToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('MaxForwards needs to be completed');

        $maxForwardsHeader = new MaxForwards();

        // @todo set some values, then test output
        $this->assertEmpty('Max-Forwards: xxx', $maxForwardsHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

