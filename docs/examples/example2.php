<?php
/**
 * Example #2 file demonstrating Wires
 * This example shows bindings using type-hinted parameters
 * and using with() to override bindings
 */
require_once dirname(__FILE__) .'/../../Wires.php';

$bindings = array(
	'_global' => array(
		'MyInterface' => 'MyClass'));

$alt_bindings = array(
	'_global' => array(
		'MyInterface' => 'MyOtherClass'));

interface MyInterface {}

class MyClass implements MyInterface {}

class MyOtherClass implements MyInterface {}

class MyFavoriteClass
{
	function __construct(MyInterface $a)
	{
		$this->a = $a;
	}
}


$i = Wires::getInjector($bindings);

$fav1 = $i->create('MyFavoriteClass');
assert('$fav1->a instanceof MyClass');

$fav2 = $i->with($alt_bindings)->create('MyFavoriteClass');
assert('$fav2->a instanceof MyOtherClass');
