<?php
require_once dirname(__FILE__) .'/../Exception.php';

class Wires_Exception_InvalidBinding extends Wires_Exception
{
	function __construct($abstract, $concrete)
	{
		parent::__construct("Invalid binding {$abstract} to {$concrete}");
	}
}