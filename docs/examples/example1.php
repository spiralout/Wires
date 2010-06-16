<?php
/**
 * Example #1 file demonstrating Wires
 * This example shows binding a parameter name to a class,
 * binding a parameter name to a value and context inheritance
 */
require_once dirname(__FILE__) .'/../../Wires.php';

$bindings = array(
      '_global' => array(
         'ExampleBase' => 'Example'),
      'ExampleBase' => array(
         'a' => array('class' => 'stdClass')),
      'Example' => array(
         'b' => array('value' => 42)));


abstract class ExampleBase
{
   function __construct($a)
   {
      $this->a = $a;
   }
}

class Example extends ExampleBase
{
   function __construct($a, $b)
   {
      parent::__construct($a);
      $this->b = $b;
   }
}


$i = Wires::getInjector($bindings);

$ex = $i->create('ExampleBase');
assert('$ex instanceof Example');
assert('$ex->a instanceof stdClass');
assert('$ex->b == 42');


