<?php

namespace ZendTest\Cache\Storage\Plugin;
use Zend\Cache,
    Zend\Cache\Storage\ExceptionEvent,
    ZendTest\Cache\Storage\TestAsset\MockAdapter,
    ArrayObject;

class ExceptionHandlerTest extends CommonPluginTest
{

    /**
     * The storage adapter
     *
     * @var Zend\Cache\Storage\Adapter\AbstractAdapter
     */
    protected $_adapter;

    public function setUp()
    {
        $this->_adapter = new MockAdapter();
        $this->_options = new Cache\Storage\Plugin\PluginOptions();
        $this->_plugin  = new Cache\Storage\Plugin\ExceptionHandler();
        $this->_plugin->setOptions($this->_options);

        parent::setUp();
    }

    public function testAddPlugin()
    {
        $this->_adapter->addPlugin($this->_plugin);

        // check attached callbacks
        $expectedListeners = array(
            'getItem.exception'  => 'onException',
            'getItems.exception' => 'onException',

            'hasItem.exception'  => 'onException',
            'hasItems.exception' => 'onException',

            'getMetadata.exception'  => 'onException',
            'getMetadatas.exception' => 'onException',

            'getDelayed.exception' => 'onException',
            'find.exception'       => 'onException',

            'fetch.exception'    => 'onException',
            'fetchAll.exception' => 'onException',

            'setItem.exception'  => 'onException',
            'setItems.exception' => 'onException',

            'addItem.exception'  => 'onException',
            'addItems.exception' => 'onException',

            'replaceItem.exception'  => 'onException',
            'replaceItems.exception' => 'onException',

            'touchItem.exception'  => 'onException',
            'touchItems.exception' => 'onException',

            'removeItem.exception'  => 'onException',
            'removeItems.exception' => 'onException',

            'checkAndSetItem.exception' => 'onException',

            'incrementItem.exception'  => 'onException',
            'incrementItems.exception' => 'onException',

            'decrementItem.exception'  => 'onException',
            'decrementItems.exception' => 'onException',

            'clear.exception'            => 'onException',
            'clearByNamespace.exception' => 'onException',

            'optimize.exception'    => 'onException',
            'getCapacity.exception' => 'onException',
        );
        foreach ($expectedListeners as $eventName => $expectedCallbackMethod) {
            $listeners = $this->_adapter->events()->getListeners($eventName);

            // event should attached only once
            $this->assertSame(1, $listeners->count());

            // check expected callback method
            $cb = $listeners->top()->getCallback();
            $this->assertArrayHasKey(0, $cb);
            $this->assertSame($this->_plugin, $cb[0]);
            $this->assertArrayHasKey(1, $cb);
            $this->assertSame($expectedCallbackMethod, $cb[1]);
        }
    }

    public function testRemovePlugin()
    {
        $this->_adapter->addPlugin($this->_plugin);
        $this->_adapter->removePlugin($this->_plugin);

        // no events should be attached
        $this->assertEquals(0, count($this->_adapter->events()->getEvents()));
    }

    public function testOnExceptionCallCallback()
    {
        $expectedException = new \Exception();
        $callbackCalled    = false;

        $this->_options->setExceptionCallback(function ($exception) use ($expectedException, &$callbackCalled) {
            $callbackCalled = ($exception === $expectedException);
        });

        // run onException
        $event = new ExceptionEvent('getItem.exception', $this->_adapter, new ArrayObject(array(
            'key'     => 'key',
            'options' => array()
        )), $expectedException);
        $this->_plugin->onException($event);

        $this->assertTrue(
            $callbackCalled,
            "Expected callback wasn't called or the expected exception wasn't the first argument"
        );
    }

    public function testDontThrowException()
    {
        $this->_options->setThrowExceptions(false);

        // run onException
        $event = new ExceptionEvent('getItem.exception', $this->_adapter, new ArrayObject(array(
            'key'     => 'key',
            'options' => array()
        )), new \Exception());
        $this->_plugin->onException($event);

        $this->assertFalse($event->getThrowException());
        $this->assertFalse($event->getResult());
    }

}
