<?php

namespace ZendTest\Mvc\TestAsset;

use Zend\Di\Locator as LocatorDefinition,
    Zend\Di\Exception\ClassNotFoundException;

/**
 * Dummy locator used to test handling of locator objects by Application
 */
class Locator implements LocatorDefinition
{
    protected $services = array();

    public function get($name, array $params = array())
    {
        if (!isset($this->services[$name])) {
            throw new ClassNotFoundException();
        }

        $service = call_user_func_array($this->services[$name], $params);
        return $service;
    }

    public function add($name, $callback)
    {
        $this->services[$name] = $callback;
    }

    public function remove($name)
    {
        if (isset($this->services[$name])) {
            unset($this->services[$name]);
        }
    }
}
