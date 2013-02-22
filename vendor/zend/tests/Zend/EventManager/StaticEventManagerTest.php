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
 * @package    Zend_EventManager
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\EventManager;
use Zend\EventManager\StaticEventManager,
    Zend\EventManager\EventManager,
    PHPUnit_Framework_TestCase as TestCase;

/**
 * @category   Zend
 * @package    Zend_EventManager
 * @subpackage UnitTests
 * @group      Zend_EventManager
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class StaticEventManagerTest extends TestCase
{
    public function setUp()
    {
        StaticEventManager::resetInstance();
    }

    public function tearDown()
    {
        StaticEventManager::resetInstance();
    }

    public function testOperatesAsASingleton()
    {
        $expected = StaticEventManager::getInstance();
        $test     = StaticEventManager::getInstance();
        $this->assertSame($expected, $test);
    }

    public function testCanResetInstance()
    {
        $original = StaticEventManager::getInstance();
        StaticEventManager::resetInstance();
        $test = StaticEventManager::getInstance();
        $this->assertNotSame($original, $test);
    }

    public function testSingletonInstanceIsInstanceOfClass()
    {
        $this->assertInstanceOf('Zend\EventManager\StaticEventManager', StaticEventManager::getInstance());
    }

    public function testCanAttachCallbackToEvent()
    {
        $events = StaticEventManager::getInstance();
        $events->attach('foo', 'bar', array($this, __FUNCTION__));
        $this->assertContains('bar', $events->getEvents('foo'));
        $expected  = array($this, __FUNCTION__);
        $found     = false;
        $listeners = $events->getListeners('foo', 'bar');
        $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $listeners);
        $this->assertTrue(0 < count($listeners), 'Empty listeners!');
        foreach ($listeners as $listener) {
            if ($expected === $listener->getCallback()) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Did not find listener!');
    }

    public function testCanAttachSameEventToMultipleResourcesAtOnce()
    {
        $events = StaticEventManager::getInstance();
        $events->attach(array('foo', 'test'), 'bar', array($this, __FUNCTION__));
        $this->assertContains('bar', $events->getEvents('foo'));
        $this->assertContains('bar', $events->getEvents('test'));
        $expected = array($this, __FUNCTION__);
        foreach (array('foo', 'test') as $id) {
            $found     = false;
            $listeners = $events->getListeners($id, 'bar');
            $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $listeners);
            $this->assertTrue(0 < count($listeners), 'Empty listeners!');
            foreach ($listeners as $listener) {
                if ($expected === $listener->getCallback()) {
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found, 'Did not find listener!');
        }
    }

    public function testCanDetachListenerFromResource()
    {
        $events = StaticEventManager::getInstance();
        $events->attach('foo', 'bar', array($this, __FUNCTION__));
        foreach ($events->getListeners('foo', 'bar') as $listener) {
            // only one; retrieving it so we can detach
        }
        $events->detach('foo', $listener);
        $listeners = $events->getListeners('foo', 'bar');
        $this->assertEquals(0, count($listeners));
    }

    public function testCanGetEventsByResource()
    {
        $events = StaticEventManager::getInstance();
        $events->attach('foo', 'bar', array($this, __FUNCTION__));
        $this->assertEquals(array('bar'), $events->getEvents('foo'));
    }

    public function testCanGetListenersByResourceAndEvent()
    {
        $events = StaticEventManager::getInstance();
        $events->attach('foo', 'bar', array($this, __FUNCTION__));
        $listeners = $events->getListeners('foo', 'bar');
        $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $listeners);
        $this->assertEquals(1, count($listeners));
    }

    public function testCanClearListenersByResource()
    {
        $events = StaticEventManager::getInstance();
        $events->attach('foo', 'bar', array($this, __FUNCTION__));
        $events->attach('foo', 'baz', array($this, __FUNCTION__));
        $events->clearListeners('foo');
        $this->assertFalse($events->getListeners('foo', 'bar'));
        $this->assertFalse($events->getListeners('foo', 'baz'));
    }

    public function testCanClearListenersByResourceAndEvent()
    {
        $events = StaticEventManager::getInstance();
        $events->attach('foo', 'bar', array($this, __FUNCTION__));
        $events->attach('foo', 'baz', array($this, __FUNCTION__));
        $events->attach('foo', 'bat', array($this, __FUNCTION__));
        $events->clearListeners('foo', 'baz');
        $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $events->getListeners('foo', 'baz'));
        $this->assertEquals(0, count($events->getListeners('foo', 'baz')));
        $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $events->getListeners('foo', 'bar'));
        $this->assertEquals(1, count($events->getListeners('foo', 'bar')));
        $this->assertInstanceOf('Zend\Stdlib\PriorityQueue', $events->getListeners('foo', 'bat'));
        $this->assertEquals(1, count($events->getListeners('foo', 'bat')));
    }

    public function testCanPassArrayOfIdentifiersToConstructor()
    {
        $identifiers = array('foo', 'bar');
        $manager = new EventManager($identifiers);
    }

    public function testListenersAttachedToAnyIdentifierProvidedToEventManagerWillBeTriggered()
    {
        $identifiers = array('foo', 'bar');
        $manager = new EventManager($identifiers);
        $events  = StaticEventManager::getInstance();
        $test    = new \stdClass;
        $test->triggered = 0;
        $events->attach('foo', 'bar', function($e) use ($test) {
            $test->triggered++;
        });
        $events->attach('bar', 'bar', function($e) use ($test) {
            $test->triggered++;
        });
        $manager->trigger('bar', $this, array());
        $this->assertEquals(2, $test->triggered);
    }
}
