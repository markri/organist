<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Code\Reflection;

use Zend\Code\Reflection\ClassReflection;


/**
 * @category   Zend
 * @package    Zend_Reflection
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group      Zend_Reflection
 * @group      Zend_Reflection_Class
 */
class ClassReflectionTest extends \PHPUnit_Framework_TestCase
{


    public function testMethodReturns()
    {

        $reflectionClass = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass2');

        $methodByName = $reflectionClass->getMethod('getProp1');
        $this->assertEquals('Zend\Code\Reflection\MethodReflection', get_class($methodByName));

        $methodsAll = $reflectionClass->getMethods();
        $this->assertEquals(3, count($methodsAll));

        $firstMethod = array_shift($methodsAll);
        $this->assertEquals('getProp1', $firstMethod->getName());
    }

    public function testPropertyReturns()
    {
        $reflectionClass = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass2');

        $propertyByName = $reflectionClass->getProperty('_prop1');
        $this->assertInstanceOf('Zend\Code\Reflection\PropertyReflection', $propertyByName);

        $propertiesAll = $reflectionClass->getProperties();
        $this->assertEquals(2, count($propertiesAll));

        $firstProperty = array_shift($propertiesAll);
        $this->assertEquals('_prop1', $firstProperty->getName());
    }

    public function testParentReturn()
    {
        $reflectionClass = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass');

        $parent = $reflectionClass->getParentClass();
        $this->assertEquals('Zend\Code\Reflection\ClassReflection', get_class($parent));
        $this->assertEquals('ArrayObject', $parent->getName());

    }

    public function testInterfaceReturn()
    {
        $reflectionClass = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass4');

        $interfaces = $reflectionClass->getInterfaces();
        $this->assertEquals(1, count($interfaces));

        $interface = array_shift($interfaces);
        $this->assertEquals('ZendTest\Code\Reflection\TestAsset\TestSampleClassInterface', $interface->getName());

    }

    public function testGetContentsReturnsContents()
    {
        $reflectionClass = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass2');
        $target = <<<EOS
{

    protected \$_prop1 = null;
    protected \$_prop2 = null;

    public function getProp1()
    {
        return \$this->_prop1;
    }

    public function getProp2(\$param1, TestSampleClass \$param2)
    {
        return \$this->_prop2;
    }

    public function getIterator()
    {
        return array();
    }

}
EOS;
        $this->assertEquals($target, $reflectionClass->getContents());
    }

    public function testStartLine()
    {
        $this->markTestIncomplete('Line numbers not complete yet');

        $reflectionClass = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass5');

        $this->assertEquals(16, $reflectionClass->getStartLine());
        $this->assertEquals(5, $reflectionClass->getStartLine(true));
    }


    public function testGetDeclaringFileReturnsFilename()
    {
        $reflectionClass = new ClassReflection('ZendTest\Code\Reflection\TestAsset\TestSampleClass2');
        $this->assertContains('TestSampleClass2.php', $reflectionClass->getDeclaringFile()->getFileName());
    }

}
