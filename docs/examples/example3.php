<?php
/**
 * Example #3 file demonstrating Wires
 * This example shows a more complex hierarchy being
 * instantiated and different binding types being used together
 */
require_once dirname(__FILE__) .'/../../Wires.php';

$bindings = array(
      '_global' => array(
         'OneInterface' => 'FirstOne',
         'AbstractFive' => 'Five',
         'ReallyAbstractThree' => 'AbstractThree'),
      'Three' => array(
         'AbstractTwo' => array('asSingleton' => true, 'class' => 'Two')),
      'FourInterface' => array(
         'OneInterface' => 'SecondOne'),
      'AbstractFive' => array(
         'three' => array('class' => 'ReallyAbstractThree')),
      'Five' => array(
         'AbstractThree' => 'Three',
         'FourInterface' => 'Four')
      );

interface OneInterface {}
class FirstOne implements OneInterface {}
class SecondOne implements OneInterface {}

abstract class AbstractTwo {}
class Two extends AbstractTwo 
{
   function __construct(OneInterface $one)
   {
      $this->one = $one;
   }
}

abstract class ReallyAbstractThree {}
abstract class AbstractThree extends ReallyAbstractThree {}
class Three extends AbstractThree
{
   function __construct(AbstractTwo $two_a, AbstractTwo $two_b)
   {
      $this->two_a = $two_a;
      $this->two_b = $two_b;
   }
}

interface FourInterface {}
class Four implements FourInterface
{
   function __construct(OneInterface $one)
   {
      $this->one = $one;
   }
}

abstract class AbstractFive
{
   function __construct($three)
   {
      $this->three = $three;
   }
}

class Five extends AbstractFive
{
   function __construct($three, FourInterface $four)
   {
      parent::__construct($three);
      $this->four = $four;
   }
}

$i = Wires::getInjector($bindings);

$five = $i->create('AbstractFive');

assert('$five->three instanceof Three');
assert('$five->three->two_a->one instanceof FirstOne');
assert('$five->three->two_a instanceof Two');
assert('$five->three->two_b instanceof Two');
assert('$five->three->two_a === $five->three->two_b');
assert('$five->four instanceof Four');
assert('$five->four->one instanceof SecondOne');

