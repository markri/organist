<?php

namespace ZendTest\Di;

use Zend\Di\Di,
    Zend\Di\DefinitionList,
    Zend\Di\InstanceManager,
    Zend\Di\Configuration,
    Zend\Di\Definition;


class DiTest extends \PHPUnit_Framework_TestCase
{

    public function testDiHasBuiltInImplementations()
    {
        $di = new Di();
        $this->assertInstanceOf('Zend\Di\InstanceManager', $di->instanceManager());

        $definitions = $di->definitions();

        $this->assertInstanceOf('Zend\Di\DefinitionList', $definitions);
        $this->assertInstanceOf('Zend\Di\Definition\RuntimeDefinition', $definitions->top());
    }

    public function testDiConstructorCanTakeDependencies()
    {
        $dl = new DefinitionList(array());
        $im = new InstanceManager();
        $cg = new Configuration(array());
        $di = new Di($dl, $im, $cg);

        $this->assertSame($dl, $di->definitions());
        $this->assertSame($im, $di->instanceManager());

        $di->setDefinitionList($dl);
        $di->setInstanceManager($im);

        $this->assertSame($dl, $di->definitions());
        $this->assertSame($im, $di->instanceManager());
    }

    public function testPassingInvalidDefinitionRaisesException()
    {
        $di = new Di();
        
        $this->setExpectedException('PHPUnit_Framework_Error');
        $di->setDefinitionList(array('foo'));
    }
    
    public function testGetRetrievesObjectWithMatchingClassDefinition()
    {
        $di = new Di();
        $obj = $di->get('ZendTest\Di\TestAsset\BasicClass');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\BasicClass', $obj);
    }
    
    public function testGetRetrievesSameInstanceOnSubsequentCalls()
    {
        $di = new Di();
        $obj1 = $di->get('ZendTest\Di\TestAsset\BasicClass');
        $obj2 = $di->get('ZendTest\Di\TestAsset\BasicClass');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\BasicClass', $obj1);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\BasicClass', $obj2);
        $this->assertSame($obj1, $obj2);
    }
    
    public function testGetThrowsExceptionWhenUnknownClassIsUsed()
    {
        $di = new Di();
        
        $this->setExpectedException('Zend\Di\Exception\ClassNotFoundException', 'could not be located in');
        $obj1 = $di->get('ZendTest\Di\TestAsset\NonExistentClass');
    }
    
    public function testGetThrowsExceptionWhenMissingParametersAreEncountered()
    {
        $di = new Di();
        
        $this->setExpectedException('Zend\Di\Exception\MissingPropertyException', 'Missing instance/object for ');
        $obj1 = $di->get('ZendTest\Di\TestAsset\BasicClassWithParam');
    }
    
    public function testNewInstanceReturnsDifferentInstances()
    {
        $di = new Di();
        $obj1 = $di->newInstance('ZendTest\Di\TestAsset\BasicClass');
        $obj2 = $di->newInstance('ZendTest\Di\TestAsset\BasicClass');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\BasicClass', $obj1);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\BasicClass', $obj2);
        $this->assertNotSame($obj1, $obj2);
    }
    
    public function testNewInstanceReturnsInstanceThatIsSharedWithGet()
    {
        $di = new Di();
        $obj1 = $di->newInstance('ZendTest\Di\TestAsset\BasicClass');
        $obj2 = $di->get('ZendTest\Di\TestAsset\BasicClass');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\BasicClass', $obj1);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\BasicClass', $obj2);
        $this->assertSame($obj1, $obj2);
    }
    
    public function testNewInstanceReturnsInstanceThatIsNotSharedWithGet()
    {
        $di = new Di();
        $obj1 = $di->newInstance('ZendTest\Di\TestAsset\BasicClass', array(), false);
        $obj2 = $di->get('ZendTest\Di\TestAsset\BasicClass');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\BasicClass', $obj1);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\BasicClass', $obj2);
        $this->assertNotSame($obj1, $obj2);
    }

    public function testNewInstanceCanHandleClassesCreatedByCallback()
    {
        $definitionList = new DefinitionList(array(
            $classdef = new Definition\ClassDefinition('ZendTest\Di\TestAsset\CallbackClasses\A'),
            new Definition\RuntimeDefinition()
        ));
        $classdef->setInstantiator('ZendTest\Di\TestAsset\CallbackClasses\A::factory');

        $di = new Di($definitionList);
        $a = $di->get('ZendTest\Di\TestAsset\CallbackClasses\A');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\CallbackClasses\A', $a);
    }

    public function testNewInstanceCanHandleComplexCallback()
    {
        $definitionList = new DefinitionList(array(
            $classdefB = new Definition\ClassDefinition('ZendTest\Di\TestAsset\CallbackClasses\B'),
            $classdefC = new Definition\ClassDefinition('ZendTest\Di\TestAsset\CallbackClasses\C'),
            new Definition\RuntimeDefinition()
        ));

        $classdefB->setInstantiator('ZendTest\Di\TestAsset\CallbackClasses\B::factory');
        $classdefB->addMethod('factory', true);
        $classdefB->addMethodParameter('factory', 'c', array('type' => 'ZendTest\Di\TestAsset\CallbackClasses\C', 'required' => true));
        $classdefB->addMethodParameter('factory', 'params', array('type' => 'Array', 'required'=>false));

        $di = new Di($definitionList);
        $b = $di->get('ZendTest\Di\TestAsset\CallbackClasses\B', array('params'=>array('foo' => 'bar')));
        $this->assertInstanceOf('ZendTest\Di\TestAsset\CallbackClasses\B', $b);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\CallbackClasses\C', $b->c);
        $this->assertEquals(array('foo' => 'bar'), $b->params);
    }


//    public function testCanSetInstantiatorToStaticFactory()
//    {
//        $config = new Configuration(array(
//            'definition' => array(
//                'class' => array(
//                    'ZendTest\Di\TestAsset\DummyParams' => array(
//                        'instantiator' => array('ZendTest\Di\TestAsset\StaticFactory', 'factory'),
//                    ),
//                    'ZendTest\Di\TestAsset\StaticFactory' => array(
//                        'methods' => array(
//                            'factory' => array(
//                                'struct' => array(
//                                    'type' => 'ZendTest\Di\TestAsset\Struct',
//                                    'required' => true,
//                                ),
//                                'params' => array(
//                                    'required' => true,
//                                ),
//                            ),
//                        ),
//                    ),
//                ),
//            ),
//            'instance' => array(
//                'ZendTest\Di\TestAsset\DummyParams' => array(
//                    'parameters' => array(
//                        'struct' => 'ZendTest\Di\TestAsset\Struct',
//                        'params' => array(
//                            'foo' => 'bar',
//                        ),
//                    ),
//                ),
//                'ZendTest\Di\TestAsset\Struct' => array(
//                    'parameters' => array(
//                        'param1' => 'hello',
//                        'param2' => 'world',
//                    ),
//                ),
//            ),
//        ));
//        $di = new Di();
//        $di->configure($config);
//        $dummyParams = $di->get('ZendTest\Di\TestAsset\DummyParams');
//        $this->assertEquals($dummyParams->params['param1'], 'hello');
//        $this->assertEquals($dummyParams->params['param2'], 'world');
//        $this->assertEquals($dummyParams->params['foo'], 'bar');
//        $this->assertArrayNotHasKey('methods', $di->definitions()->hasMethods('ZendTest\Di\TestAsset\StaticFactory'));
//    }

    /**
     * @group ConstructorInjection
     */
    public function testGetWillResolveConstructorInjectionDependencies()
    {
        $di = new Di();
        $b = $di->get('ZendTest\Di\TestAsset\ConstructorInjection\B');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\ConstructorInjection\B', $b);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\ConstructorInjection\A', $b->a);
    }
    
    /**
     * @group ConstructorInjection
     */
    public function testGetWillResolveConstructorInjectionDependenciesAndInstanceAreTheSame()
    {
        $di = new Di();
        $b = $di->get('ZendTest\Di\TestAsset\ConstructorInjection\B');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\ConstructorInjection\B', $b);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\ConstructorInjection\A', $b->a);
        
        $b2 = $di->get('ZendTest\Di\TestAsset\ConstructorInjection\B');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\ConstructorInjection\B', $b2);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\ConstructorInjection\A', $b2->a);
        
        $this->assertSame($b, $b2);
        $this->assertSame($b->a, $b2->a);
    }
    
    /**
     * @group ConstructorInjection
     */
    public function testNewInstanceWillResolveConstructorInjectionDependencies()
    {
        $di = new Di();
        $b = $di->newInstance('ZendTest\Di\TestAsset\ConstructorInjection\B');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\ConstructorInjection\B', $b);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\ConstructorInjection\A', $b->a);
    }
    
    /**
     * @group ConstructorInjection
     */
    public function testNewInstanceWillResolveConstructorInjectionDependenciesWithProperties()
    {
        $di = new Di();
        
        $im = $di->instanceManager();
        $im->setParameters('ZendTest\Di\TestAsset\ConstructorInjection\X', array('one' => 1, 'two' => 2));
        
        $y = $di->newInstance('ZendTest\Di\TestAsset\ConstructorInjection\Y');
        $this->assertEquals(1, $y->x->one);
        $this->assertEquals(2, $y->x->two);
    }
    
    /**
     * @group ConstructorInjection
     */
    public function testNewInstanceWillThrowExceptionOnConstructorInjectionDependencyWithMissingParameter()
    {
        $di = new Di();
        
        $this->setExpectedException('Zend\Di\Exception\MissingPropertyException', 'Missing instance/object for parameter');
        $b = $di->newInstance('ZendTest\Di\TestAsset\ConstructorInjection\X');
    }
    
    /**
     * @group ConstructorInjection
     */
    public function testNewInstanceWillResolveConstructorInjectionDependenciesWithMoreThan2Dependencies()
    {
        $di = new Di();
        
        $im = $di->instanceManager();
        $im->setParameters('ZendTest\Di\TestAsset\ConstructorInjection\X', array('one' => 1, 'two' => 2));
        
        $z = $di->newInstance('ZendTest\Di\TestAsset\ConstructorInjection\Z');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\ConstructorInjection\Y', $z->y);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\ConstructorInjection\X', $z->y->x);
    }
    
    /**
     * @group SetterInjection
     */
    public function testGetWillResolveSetterInjectionDependencies()
    {
        $di = new Di();
        $b = $di->get('ZendTest\Di\TestAsset\SetterInjection\B');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\SetterInjection\B', $b);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\SetterInjection\A', $b->a);
    }
    
    /**
     * @group SetterInjection
     */
    public function testGetWillResolveSetterInjectionDependenciesAndInstanceAreTheSame()
    {
        $di = new Di();
        $b = $di->get('ZendTest\Di\TestAsset\SetterInjection\B');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\SetterInjection\B', $b);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\SetterInjection\A', $b->a);
        
        $b2 = $di->get('ZendTest\Di\TestAsset\SetterInjection\B');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\SetterInjection\B', $b2);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\SetterInjection\A', $b2->a);
        
        $this->assertSame($b, $b2);
        $this->assertSame($b->a, $b2->a);
    }
    
    /**
     * @group SetterInjection
     */
    public function testNewInstanceWillResolveSetterInjectionDependencies()
    {
        $di = new Di();
        $b = $di->newInstance('ZendTest\Di\TestAsset\SetterInjection\B');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\SetterInjection\B', $b);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\SetterInjection\A', $b->a);
    }
    
    /**
     * @todo Setter Injections is not automatic, find a way to test this logically
     *
     * @group SetterInjection
     */
    public function testNewInstanceWillResolveSetterInjectionDependenciesWithProperties()
    {
        $di = new Di();
        
        $im = $di->instanceManager();
        $im->setParameters('ZendTest\Di\TestAsset\SetterInjection\X', array('one' => 1, 'two' => 2));

        $x = $di->get('ZendTest\Di\TestAsset\SetterInjection\X');
        $y = $di->newInstance('ZendTest\Di\TestAsset\SetterInjection\Y', array('x' => $x));

        $this->assertEquals(1, $y->x->one);
        $this->assertEquals(2, $y->x->two);
    }
    
    /**
     * Test for Circular Dependencies (case 1)
     * 
     * A->B, B->A
     * @group CircularDependencyCheck
     */
    public function testNewInstanceThrowsExceptionOnBasicCircularDependency()
    {
        $di = new Di();

        $this->setExpectedException('Zend\Di\Exception\CircularDependencyException');
        $di->newInstance('ZendTest\Di\TestAsset\CircularClasses\A');
    }
    
    /**
     * Test for Circular Dependencies (case 2)
     * 
     * C->D, D->E, E->C
     * @group CircularDependencyCheck
     */
    public function testNewInstanceThrowsExceptionOnThreeLevelCircularDependency()
    {
        $di = new Di();

        $this->setExpectedException(
            'Zend\Di\Exception\CircularDependencyException',
            'Circular dependency detected: ZendTest\Di\TestAsset\CircularClasses\E depends on ZendTest\Di\TestAsset\CircularClasses\C and viceversa'
        );
        $di->newInstance('ZendTest\Di\TestAsset\CircularClasses\C');
    }
    
    /**
     * Test for Circular Dependencies (case 2)
     * 
     * C->D, D->E, E->C
     * @group CircularDependencyCheck
     */
    public function testNewInstanceThrowsExceptionWhenEnteringInMiddleOfCircularDependency()
    {
        $di = new Di();

        $this->setExpectedException(
            'Zend\Di\Exception\CircularDependencyException',
            'Circular dependency detected: ZendTest\Di\TestAsset\CircularClasses\C depends on ZendTest\Di\TestAsset\CircularClasses\D and viceversa'
        );
        $di->newInstance('ZendTest\Di\TestAsset\CircularClasses\D');
    }
    
    /**
     * Fix for PHP bug in is_subclass_of
     * 
     * @see https://bugs.php.net/bug.php?id=53727
     */
    public function testNewInstanceWillUsePreferredClassForInterfaceHints()
    {
        $di = new Di();
        $di->instanceManager()->addTypePreference(
            'ZendTest\Di\TestAsset\PreferredImplClasses\A',
            'ZendTest\Di\TestAsset\PreferredImplClasses\BofA'
        );
        
        $c = $di->get('ZendTest\Di\TestAsset\PreferredImplClasses\C');
        $a = $c->a;
        $this->assertInstanceOf('ZendTest\Di\TestAsset\PreferredImplClasses\BofA', $a);
        $d = $di->get('ZendTest\Di\TestAsset\PreferredImplClasses\D');
        $this->assertSame($a, $d->a);
    }

    public function testInjectionInstancesCanBeInjectedMultipleTimes()
    {
        $definitionList = new DefinitionList(array(
            $classdef = new Definition\ClassDefinition('ZendTest\Di\TestAsset\InjectionClasses\A'),
            new Definition\RuntimeDefinition()
        ));
        $classdef->addMethod('addB');
        $classdef->addMethodParameter('addB', 'b', array('required' => true, 'type' => 'ZendTest\Di\TestAsset\InjectionClasses\B'));

        $di = new Di($definitionList);
        $di->instanceManager()->setInjections(
            'ZendTest\Di\TestAsset\InjectionClasses\A',
            array(
                'ZendTest\Di\TestAsset\InjectionClasses\B'
            )
        );
        $a = $di->newInstance('ZendTest\Di\TestAsset\InjectionClasses\A');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\InjectionClasses\B', $a->bs[0]);

        $di = new Di($definitionList);
        $di->instanceManager()->addAlias('my-b1', 'ZendTest\Di\TestAsset\InjectionClasses\B');
        $di->instanceManager()->addAlias('my-b2', 'ZendTest\Di\TestAsset\InjectionClasses\B');

        $di->instanceManager()->setInjections(
            'ZendTest\Di\TestAsset\InjectionClasses\A',
            array(
                'my-b1',
                'my-b2'
            )
        );
        $a = $di->newInstance('ZendTest\Di\TestAsset\InjectionClasses\A');

        $this->assertInstanceOf('ZendTest\Di\TestAsset\InjectionClasses\B', $a->bs[0]);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\InjectionClasses\B', $a->bs[1]);
        $this->assertNotSame(
            $a->bs[0],
            $a->bs[1]
        );
    }

    public function testInjectionCanHandleDisambiguation()
    {
        $definitionList = new DefinitionList(array(
            $classdef = new Definition\ClassDefinition('ZendTest\Di\TestAsset\InjectionClasses\A'),
            new Definition\RuntimeDefinition()
        ));
        $classdef->addMethod('injectBOnce');
        $classdef->addMethod('injectBTwice');
        $classdef->addMethodParameter('injectBOnce', 'b', array('required' => true, 'type' => 'ZendTest\Di\TestAsset\InjectionClasses\B'));
        $classdef->addMethodParameter('injectBTwice', 'b', array('required' => true, 'type' => 'ZendTest\Di\TestAsset\InjectionClasses\B'));

        $di = new Di($definitionList);
        $di->instanceManager()->setInjections(
            'ZendTest\Di\TestAsset\InjectionClasses\A',
            array(
                'ZendTest\Di\TestAsset\InjectionClasses\A::injectBOnce:0' => new \ZendTest\Di\TestAsset\InjectionClasses\B('once'),
                'ZendTest\Di\TestAsset\InjectionClasses\A::injectBTwice:0' => new \ZendTest\Di\TestAsset\InjectionClasses\B('twice')
            )
        );
        $a = $di->newInstance('ZendTest\Di\TestAsset\InjectionClasses\A');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\InjectionClasses\B', $a->bs[0]);
        $this->assertEquals('once', $a->bs[0]->id);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\InjectionClasses\B', $a->bs[1]);
        $this->assertEquals('twice', $a->bs[1]->id);
    }

    public function testInjectionCanHandleMultipleInjectionsWithMultipleArguments()
    {
        $definitionList = new DefinitionList(array(
            $classdef = new Definition\ClassDefinition('ZendTest\Di\TestAsset\InjectionClasses\A'),
            new Definition\RuntimeDefinition()
        ));
        $classdef->addMethod('injectSplitDependency');
        $classdef->addMethodParameter('injectSplitDependency', 'b', array('required' => true, 'type' => 'ZendTest\Di\TestAsset\InjectionClasses\B'));
        $classdef->addMethodParameter('injectSplitDependency', 'somestring', array('required' => true, 'type' => null));

        /**
         * First test that this works with a single call
         */
        $di = new Di($definitionList);
        $di->instanceManager()->setInjections(
            'ZendTest\Di\TestAsset\InjectionClasses\A',
            array(
                'injectSplitDependency' => array('b' => 'ZendTest\Di\TestAsset\InjectionClasses\B', 'somestring' => 'bs-id')
            )
        );
        $a = $di->newInstance('ZendTest\Di\TestAsset\InjectionClasses\A');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\InjectionClasses\B', $a->bs[0]);
        $this->assertEquals('bs-id', $a->bs[0]->id);

        /**
         * Next test that this works with multiple calls
         */
        $di = new Di($definitionList);
        $di->instanceManager()->setInjections(
            'ZendTest\Di\TestAsset\InjectionClasses\A',
            array(
                'injectSplitDependency' => array(
                    array('b' => 'ZendTest\Di\TestAsset\InjectionClasses\B', 'somestring' => 'bs-id'),
                    array('b' => 'ZendTest\Di\TestAsset\InjectionClasses\C', 'somestring' => 'bs-id-for-c')
                )
            )
        );
        $a = $di->newInstance('ZendTest\Di\TestAsset\InjectionClasses\A');
        $this->assertInstanceOf('ZendTest\Di\TestAsset\InjectionClasses\B', $a->bs[0]);
        $this->assertEquals('bs-id', $a->bs[0]->id);
        $this->assertInstanceOf('ZendTest\Di\TestAsset\InjectionClasses\C', $a->bs[1]);
        $this->assertEquals('bs-id-for-c', $a->bs[1]->id);
    }
    
}
