<?php
require_once dirname(__FILE__) .'/../Locator.php';

class LocatorTest extends PHPUnit_Framework_TestCase
{
   function setUp()
   {
      $this->locator = new Wires_Locator;
   }

   function test_LoadBindings_Simple()
   {
      $this->locator->loadBindings(array('context' => array('abstract' => 'concrete')));

      $this->assertEquals($this->locator->getBinding('abstract', 'context'), 'concrete');
   }

   function test_LoadBindings_Singleton()
   {
      $this->locator->loadBindings(array('context' => array('abstract' => array('asSingleton' => true, 'class' => 'concrete'))));

      $this->assertEquals($this->locator->getBinding('abstract', 'context'), 'concrete');
   }

   function test_LoadBindings_Class()
   {
      $this->locator->loadBindings(array('context' => array('param' => array('class' => 'concrete'))));

      $this->assertEquals($this->locator->getClass('param', 'context'), 'concrete');
   }

   function test_LoadBindings_Value()
   {
      $this->locator->loadBindings(array('context' => array('param' => array('value' => 42))));

      $this->assertEquals($this->locator->getValue('param', 'context'), 42);
   }

   function test_LoadBindings_Bogus_Data_Throws_Exception()
   {
      $this->setExpectedException('Wires_Exception_InvalidBinding');

      $this->locator->loadBindings(array('context' => array('bill' => array('bogus' => 'ted'))));
   }

   function test_Bind()
   {
      $this->locator->bind('abstract', 'concrete');

      $this->assertEquals($this->locator->getBinding('abstract'), 'concrete');
   }

   function test_Bind_Already_Bound_Throws_Exception()
   {
      $this->setExpectedException('Wires_Exception_AlreadyBound');

      $this->locator->bind('abstract', 'concrete');
      $this->locator->bind('abstract', 'concrete');
   }

   function test_Bind_Already_Bound_With_Overwrite_Does_Not_Throw_Exception()
   {
      $this->locator->bind('abstract', 'concrete');
      $this->locator->bind('abstract', 'concrete', 'context', true);

      $this->assertEquals($this->locator->getBinding('abstract', 'context'), 'concrete');   	
   }

   function test_BindAsSingleton()
   {
      $this->locator->bindAsSingleton('abstract', 'concrete');

      $this->assertEquals($this->locator->getBinding('abstract'), 'concrete');
   }

   function test_BindAsSingleton_Already_Bound_Throws_Exception()
   {
      $this->setExpectedException('Wires_Exception_AlreadyBound');

      $this->locator->bindAsSingleton('abstract', 'concrete');
      $this->locator->bindAsSingleton('abstract', 'concrete');
   }

   function test_BindAsSingleton_Already_Bound_With_Overwrite_Does_Not_Throw_Exception()
   {
      $this->locator->bindAsSingleton('abstract', 'concrete');
      $this->locator->bindAsSingleton('abstract', 'concrete', 'context', true);

      $this->assertEquals($this->locator->getBinding('abstract', 'context'), 'concrete');   	
   }

   function test_BindInstance()
   {
      $this->locator->bindInstance('abstract', new stdClass);

      $this->assertEquals($this->locator->getBinding('abstract'), 'stdClass');
   }

   function test_BindInstance_Already_Bound_Throws_Exception()
   {
      $this->setExpectedException('Wires_Exception_AlreadyBound');

      $this->locator->bindInstance('abstract', new stdClass);
      $this->locator->bindInstance('abstract', new stdClass);
   }

   function test_BindInstance_Already_Bound_With_Overwrite_Does_Not_Throw_Exception()
   {
      $this->locator->bindInstance('abstract', new stdClass);
      $this->locator->bindInstance('abstract', new stdClass, 'context', true);

      $this->assertEquals($this->locator->getBinding('abstract', 'context'), 'stdClass');   	
   }

   function test_BindClass()
   {
      $this->locator->bindClass('param', 'ClassName', 'context');

      $this->assertEquals($this->locator->getClass('param', 'context'), 'ClassName');
   }

   function test_BindClass_Already_Bound_Throws_Exception()
   {
      $this->setExpectedException('Wires_Exception_AlreadyBound');

      $this->locator->bindClass('param', 'ClassName', 'context');
      $this->locator->bindClass('param', 'ClassName', 'context');
   }

   function test_BindClass_Already_Bound_With_Overwrite_Does_Not_Throw_Exception()
   {
      $this->locator->bindClass('param', 'concrete');
      $this->locator->bindClass('param', 'concrete', 'context', true);

      $this->assertEquals($this->locator->getClass('param', 'context'), 'concrete');   	
   }

   function test_BindValue()
   {
      $this->locator->bindValue('param', 42, 'context');

      $this->assertEquals($this->locator->getValue('param', 'context'), 42);
   }

   function test_BindValue_Already_Bound_Throws_Exception()
   {
      $this->setExpectedException('Wires_Exception_AlreadyBound');

      $this->locator->bindValue('param', 42, 'context');
      $this->locator->bindValue('param', 42, 'context');
   }

   function test_BindValue_Already_Bound_With_Overwrite_Does_Not_Throw_Exception()
   {
      $this->locator->bindValue('param', 42);
      $this->locator->bindValue('param', 42, 'context', true);

      $this->assertEquals($this->locator->getValue('param', 'context'), 42);   	
   }

   function test_GetClass()
   {
      $this->locator->bindClass('param', 'ClassName', 'context');

      $this->assertEquals($this->locator->getClass('param', 'context'), 'ClassName');
   }

   function test_GetClass_Unbound_Throws_Exception()
   {
      $this->setExpectedException('Wires_Exception_NotBound');
      $this->locator->getClass('param');   	
   }

   function test_GetValue()
   {
      $this->locator->bindValue('param', 42, 'context');

      $this->assertEquals($this->locator->getValue('param', 'context'), 42);
   }

   function test_GetValue_Unbound_Throws_Exception()
   {
      $this->setExpectedException('Wires_Exception_NotBound');
      $this->locator->getValue('param');   	
   }

   function test_GetInstance()
   {
      $this->locator->bindInstance('Abstract', new stdClass);
      $std = $this->locator->getInstance('Abstract');

      $this->assertTrue($std instanceof stdClass);
   }

   function test_GetInstance_Unbound_Throws_Exception()
   {
      $this->setExpectedException('Wires_Exception_NotBound');
      $this->locator->getInstance('Abstract');   	
   }

   function test_Bound()
   {
      $this->assertFalse($this->locator->bound('Abstract'));

      $this->locator->bindClass('Abstract', 'Concrete');

      $this->assertTrue($this->locator->boundClass('Abstract'));   	
   }

   function test_BoundSingleton()
   {
      $this->assertFalse($this->locator->boundSingleton('Abstract'));

      $this->locator->bindAsSingleton('Abstract', 'Concrete');

      $this->assertTrue($this->locator->boundSingleton('Abstract'));   	
   }

   function test_BoundClass()
   {
      $this->assertFalse($this->locator->boundClass('param'));

      $this->locator->bindClass('param', 'stdClass');

      $this->assertTrue($this->locator->boundClass('param'));
   }

   function test_BoundValue()
   {
      $this->assertFalse($this->locator->boundValue('param'));

      $this->locator->bindValue('param', 42);

      $this->assertTrue($this->locator->boundValue('param'));
   }

   /** @var LocatorInterface **/
   private $locator;
}
