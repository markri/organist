<?php

namespace Zend\Di\Definition\Builder;

class InjectionMethod
{
    const PARAMETER_POSTION_NEXT = 'next';
    
    protected $name = null;
    protected $parameters = array();
    
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function addParameter($name, $class = null, $position = self::PARAMETER_POSTION_NEXT)
    {
        if ($position == self::PARAMETER_POSTION_NEXT) {
            $this->parameters[$name] = $class;
        } else {
            throw new \Exception('Implementation for parameter placement is incomplete');
        }
        return $this;
    }
    
    public function getParameters()
    {
        return $this->parameters;
    }
    
}
