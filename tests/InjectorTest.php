<?php
require_once dirname(__FILE__) .'/../Injector.php';
require_once dirname(__FILE__) .'/../Locator.php';

class InjectorTest extends PHPUnit_Framework_TestCase
{
	function test_Create()
	{
		$injector = new Wires_Injector(new Wires_Locator($this->bindings));
		$test = $injector->create('Test1');

		$this->assertTrue($test instanceof Test1);
		$this->assertTrue($test->a instanceof stdClass);
	}
	
	function test_With()
	{
		$injector = new Wires_Injector(new Wires_Locator);
		$injector2 = $injector->with($this->bindings);

		$this->assertTrue($injector2 instanceof Wires_Injector);
	}
	
	private $bindings = array('Test1' => array('a' => array('class' => 'stdClass')));
}

class Test1
{
	function __construct($a)
	{
		$this->a = $a;
	}
}