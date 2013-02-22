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
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache,
    Zend\Cache\Exception\RuntimeException;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class AbstractAdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Mock of the abstract storage adapter
     *
     * @var Zend\Cache\Storage\Adapter\AbstractAdapter
     */
    protected $_storage;

    public function setUp()
    {
        $this->_options = new Cache\Storage\Adapter\AdapterOptions();
        $this->_storage = $this->getMockForAbstractClass('Zend\Cache\Storage\Adapter\AbstractAdapter');
        $this->_storage->expects($this->any())
                       ->method('getOptions')
                       ->will($this->returnValue($this->_options));
    }

    public function testGetOptions()
    {
        $options = $this->_storage->getOptions();
        $this->assertInstanceOf('Zend\Cache\Storage\Adapter\AdapterOptions', $options);
        $this->assertInternalType('boolean', $options->getWritable());
        $this->assertInternalType('boolean', $options->getReadable());
        $this->assertInternalType('integer', $options->getTtl());
        $this->assertInternalType('string', $options->getNamespace());
        $this->assertInternalType('string', $options->getNamespacePattern());
        $this->assertInternalType('string', $options->getKeyPattern());
        $this->assertInternalType('boolean', $options->getIgnoreMissingItems());
    }

    public function testSetWritable()
    {
        $this->_options->setWritable(true);
        $this->assertTrue($this->_options->getWritable());

        $this->_options->setWritable(false);
        $this->assertFalse($this->_options->getWritable());
    }

    public function testSetReadable()
    {
        $this->_options->setReadable(true);
        $this->assertTrue($this->_options->getReadable());

        $this->_options->setReadable(false);
        $this->assertFalse($this->_options->getReadable());
    }

    public function testSetTtl()
    {
        $this->_options->setTtl('123');
        $this->assertSame(123, $this->_options->getTtl());
    }

    public function testSetTtlThrowsInvalidArgumentException()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setTtl(-1);
    }

    public function testGetDefaultNamespaceNotEmpty()
    {
        $ns = $this->_options->getNamespace();
        $this->assertNotEmpty($ns);
    }

    public function testSetNamespace()
    {
        $this->_options->setNamespace('new_namespace');
        $this->assertSame('new_namespace', $this->_options->getNamespace());
    }

    public function testSetNamespacePattern()
    {
        $pattern = '/^.*$/';
        $this->_options->setNamespacePattern($pattern);
        $this->assertEquals($pattern, $this->_options->getNamespacePattern());
    }

    public function testUnsetNamespacePattern()
    {
        $this->_options->setNamespacePattern(null);
        $this->assertSame('', $this->_options->getNamespacePattern());
    }

    public function testSetNamespace0()
    {
        $this->_options->setNamespace('0');
        $this->assertSame('0', $this->_options->getNamespace());
    }

    public function testSetEmptyNamespaceThrowsException()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setNamespace('');
    }

    public function testSetNamespacePatternThrowsExceptionOnInvalidPattern()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setNamespacePattern('#');
    }

    public function testSetNamespacePatternThrowsExceptionOnInvalidNamespace()
    {
        $this->_options->setNamespace('ns');
        $this->setExpectedException('Zend\Cache\Exception\RuntimeException');
        $this->_options->setNamespacePattern('/[abc]/');
    }

    public function testSetKeyPattern()
    {
        $this->_options->setKeyPattern('/^[key]+$/Di');
        $this->assertEquals('/^[key]+$/Di', $this->_options->getKeyPattern());
    }

    public function testUnsetKeyPattern()
    {
        $this->_options->setKeyPattern(null);
        $this->assertSame('', $this->_options->getKeyPattern());
    }

    public function testSetKeyPatternThrowsExceptionOnInvalidPattern()
    {
        $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        $this->_options->setKeyPattern('#');
    }

    public function testSetIgnoreMissingItems()
    {
        $this->_options->setIgnoreMissingItems(true);
        $this->assertTrue($this->_options->getIgnoreMissingItems());

        $this->_options->setIgnoreMissingItems(false);
        $this->assertFalse($this->_options->getIgnoreMissingItems());
    }

    public function testPluginRegistry()
    {
        $plugin = new \ZendTest\Cache\Storage\TestAsset\MockPlugin();

        // no plugin registered
        $this->assertFalse($this->_storage->hasPlugin($plugin));
        $this->assertEquals(0, count($this->_storage->getPlugins()));
        $this->assertEquals(0, count($plugin->getHandles()));

        // register a plugin
        $this->assertSame($this->_storage, $this->_storage->addPlugin($plugin));
        $this->assertTrue($this->_storage->hasPlugin($plugin));
        $this->assertEquals(1, count($this->_storage->getPlugins()));

        // test registered callback handles
        $handles = $plugin->getHandles();
        $this->assertEquals(1, count($handles));
        $this->assertEquals(count($plugin->getEventCallbacks()), count(current($handles)));

        // test unregister a plugin
        $this->assertSame($this->_storage, $this->_storage->removePlugin($plugin));
        $this->assertFalse($this->_storage->hasPlugin($plugin));
        $this->assertEquals(0, count($this->_storage->getPlugins()));
        $this->assertEquals(0, count($plugin->getHandles()));
    }

    public function testInternalTriggerPre()
    {
        $plugin = new \ZendTest\Cache\Storage\TestAsset\MockPlugin();
        $this->_storage->addPlugin($plugin);

        $params = new \ArrayObject(array(
            'key'   => 'key1',
            'value' => 'value1'
        ));

        // call protected method
        $method = new \ReflectionMethod(get_class($this->_storage), 'triggerPre');
        $method->setAccessible(true);
        $rsCollection = $method->invoke($this->_storage, 'setItem', $params);
        $this->assertInstanceOf('Zend\EventManager\ResponseCollection', $rsCollection);

        // test called event
        $calledEvents = $plugin->getCalledEvents();
        $this->assertEquals(1, count($calledEvents));

        $event = current($calledEvents);
        $this->assertInstanceOf('Zend\Cache\Storage\Event', $event);
        $this->assertEquals('setItem.pre', $event->getName());
        $this->assertSame($this->_storage, $event->getTarget());
        $this->assertSame($params, $event->getParams());
    }

    public function testInternalTriggerPost()
    {
        $plugin = new \ZendTest\Cache\Storage\TestAsset\MockPlugin();
        $this->_storage->addPlugin($plugin);

        $params = new \ArrayObject(array(
            'key'   => 'key1',
            'value' => 'value1'
        ));
        $result = true;

        // call protected method
        $method = new \ReflectionMethod(get_class($this->_storage), 'triggerPost');
        $method->setAccessible(true);
        $result = $method->invokeArgs($this->_storage, array('setItem', $params, &$result));

        // test called event
        $calledEvents = $plugin->getCalledEvents();
        $this->assertEquals(1, count($calledEvents));
        $event = current($calledEvents);

        // return value of triggerPost and the called event should be the same
        $this->assertSame($result, $event->getResult());

        $this->assertInstanceOf('Zend\Cache\Storage\PostEvent', $event);
        $this->assertEquals('setItem.post', $event->getName());
        $this->assertSame($this->_storage, $event->getTarget());
        $this->assertSame($params, $event->getParams());
        $this->assertSame($result, $event->getResult());
    }

    public function testInternalTriggerExceptionThrowRuntimeException()
    {
        $plugin = new \ZendTest\Cache\Storage\TestAsset\MockPlugin();
        $this->_storage->addPlugin($plugin);

        $params = new \ArrayObject(array(
            'key'   => 'key1',
            'value' => 'value1'
        ));

        // call protected method
        $method = new \ReflectionMethod(get_class($this->_storage), 'triggerException');
        $method->setAccessible(true);

        $this->setExpectedException('Zend\Cache\Exception\RuntimeException', 'test');
        $method->invokeArgs($this->_storage, array('setItem', $params, new RuntimeException('test')));
    }

    public function testGetItems()
    {
        $options    = array('ttl' => 123);
        $items      = array(
            'key1'  => 'value1',
            'dKey1' => false,
            'key2'  => 'value2',
        );

        $i = 0;
        foreach ($items as $k => $v) {
            $this->_storage->expects($this->at($i++))
                ->method('getItem')
                ->with($this->equalTo($k), $this->equalTo($options))
                ->will($this->returnValue($v));
        }

        $rs = $this->_storage->getItems(array_keys($items), $options);

        // remove missing items from array to test
        $expected = $items;
        foreach ($expected as $key => $value) {
            if (false === $value) {
                unset($expected[$key]);
            }
        }

        $this->assertEquals($expected, $rs);
    }

    public function testGetMetadatas()
    {
        $options    = array('ttl' => 123);
        $items      = array(
            'key1'  => array('meta1' => 1),
            'dKey1' => false,
            'key2'  => array('meta2' => 2),
        );

        $i = 0;
        foreach ($items as $k => $v) {
            $this->_storage->expects($this->at($i++))
                ->method('getMetadata')
                ->with($this->equalTo($k), $this->equalTo($options))
                ->will($this->returnValue($v));
        }

        $rs = $this->_storage->getMetadatas(array_keys($items), $options);

        // remove missing items from array to test
        $expected = $items;
        foreach ($expected as $key => $value) {
            if (false === $value) {
                unset($expected[$key]);
            }
        }

        $this->assertEquals($expected, $rs);
    }

    public function testHasItem()
    {
        $this->_storage->expects($this->at(0))
                       ->method('getItem')
                       ->with($this->equalTo('key'))
                       ->will($this->returnValue('value'));

        $this->assertTrue($this->_storage->hasItem('key'));
    }

    public function testHasItems()
    {
        $keys = array('key1', 'key2', 'key3');

        foreach ($keys as $i => $key) {
            $this->_storage->expects($this->at($i))
                           ->method('getItem')
                           ->with($this->equalTo($key))
                           ->will(
                               ($i % 2) ? $this->returnValue('value')
                                        : $this->returnValue(false)
                           );
        }

        $rs = $this->_storage->hasItems($keys);
        $this->assertInternalType('array', $rs);
        $this->assertEquals(floor(count($keys) / 2), count($rs));
    }

    public function testSetItems()
    {
        $options = array('ttl' => 123);
        $items   = array(
            'key1' => 'value1',
            'key2' => 'value2'
        );

        $this->_storage->expects($this->exactly(count($items)))
            ->method('setItem')
            ->with($this->stringContains('key'), $this->stringContains('value'), $this->equalTo($options))
            ->will($this->returnValue(true));

        $this->assertTrue($this->_storage->setItems($items, $options));
    }

    public function testSetItemsFail()
    {
        $options = array('ttl' => 123);
        $items   = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        );

        $this->_storage->expects($this->exactly(count($items)))
            ->method('setItem')
            ->with($this->stringContains('key'), $this->stringContains('value'), $this->equalTo($options))
            ->will($this->returnValue(false));

        $this->assertFalse($this->_storage->setItems($items, $options));
    }

    public function testAddItems()
    {
        $options = array('ttl' => 123);
        $items   = array(
            'key1' => 'value1',
            'key2' => 'value2'
        );

        // add -> has -> get -> set
        $this->_storage->expects($this->exactly(count($items)))
            ->method('getItem')
            ->with($this->stringContains('key'), $this->equalTo($options))
            ->will($this->returnValue(false));
        $this->_storage->expects($this->exactly(count($items)))
            ->method('setItem')
            ->with($this->stringContains('key'), $this->stringContains('value'), $this->equalTo($options))
            ->will($this->returnValue(true));

        $this->assertTrue($this->_storage->addItems($items, $options));
    }

    public function testAddItemsFail()
    {
        $options = array('ttl' => 123);
        $items   = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        );

        // add -> has -> get -> set
        $this->_storage->expects($this->exactly(count($items)))
            ->method('getItem')
            ->with($this->stringContains('key'), $this->equalTo($options))
            ->will($this->returnValue(false));
        $this->_storage->expects($this->exactly(count($items)))
            ->method('setItem')
            ->with($this->stringContains('key'), $this->stringContains('value'), $this->equalTo($options))
            ->will($this->returnValue(false));

        $this->assertFalse($this->_storage->addItems($items, $options));
    }

    public function testReplaceItems()
    {
        $options = array('ttl' => 123);
        $items   = array(
            'key1' => 'value1',
            'key2' => 'value2'
        );

        // replace -> has -> get -> set
        $this->_storage->expects($this->exactly(count($items)))
            ->method('getItem')
            ->with($this->stringContains('key'), $this->equalTo($options))
            ->will($this->returnValue(true));
        $this->_storage->expects($this->exactly(count($items)))
            ->method('setItem')
            ->with($this->stringContains('key'), $this->stringContains('value'), $this->equalTo($options))
            ->will($this->returnValue(true));

        $this->assertTrue($this->_storage->replaceItems($items, $options));
    }

    public function testReplaceItemsFail()
    {
        $options = array('ttl' => 123);
        $items   = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        );

        // replace -> has -> get -> set
        $this->_storage->expects($this->exactly(count($items)))
            ->method('getItem')
            ->with($this->stringContains('key'), $this->equalTo($options))
            ->will($this->returnValue(true));
        $this->_storage->expects($this->exactly(count($items)))
            ->method('setItem')
            ->with($this->stringContains('key'), $this->stringContains('value'), $this->equalTo($options))
            ->will($this->returnValue(false));

        $this->assertFalse($this->_storage->replaceItems($items, $options));
    }

    public function testRemoveItems()
    {
        $options = array('ttl' => 123);
        $keys    = array('key1', 'key2');

        foreach ($keys as $i => $key) {
            $this->_storage->expects($this->at($i))
                           ->method('removeItem')
                           ->with($this->equalTo($key), $this->equalTo($options))
                           ->will($this->returnValue(true));
        }

        $this->assertTrue($this->_storage->removeItems($keys, $options));
    }

    public function testRemoveItemsFail()
    {
        $options = array('ttl' => 123);
        $items   = array('key1', 'key2', 'key3');

        $this->_storage->expects($this->at(0))
                       ->method('removeItem')
                       ->with($this->equalTo('key1'), $this->equalTo($options))
                       ->will($this->returnValue(true));
        $this->_storage->expects($this->at(1))
                       ->method('removeItem')
                       ->with($this->equalTo('key2'), $this->equalTo($options))
                       ->will($this->returnValue(false)); // -> fail
        $this->_storage->expects($this->at(2))
                       ->method('removeItem')
                       ->with($this->equalTo('key3'), $this->equalTo($options))
                       ->will($this->returnValue(true));

        $this->assertFalse($this->_storage->removeItems($items, $options));
    }

    // TODO: getDelayed + fatch[All]
    // TODO: incrementItem[s] + decrementItem[s]
    // TODO: touchItem[s]

}
