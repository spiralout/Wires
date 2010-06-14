<?php
require_once 'PHPUnit/Framework/TestSuite.php';
require_once dirname(__FILE__) .'/InjectorTest.php';
require_once dirname(__FILE__) .'/LocatorTest.php';

class AllTests
{
	static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Wires');
		$suite->addTestSuite('InjectorTest');
		$suite->addTestSuite('LocatorTest');
		
		return $suite;
	}	
}