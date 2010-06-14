<?php
require_once dirname(__FILE__) .'/Injector.php';
require_once dirname(__FILE__) .'/Locator.php';

class Wires
{
	static function getInjector($bindings = null)
	{
		return new Wires_Injector(new Wires_Locator($bindings));
	}
}