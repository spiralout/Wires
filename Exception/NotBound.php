<?php
require_once dirname(__FILE__) .'/../Exception.php';

class Wires_Exception_NotBound extends Wires_Exception
{
	function __construct($abstract, $context)
	{
		parent::__construct("{$abstract} not bound in context {$context}");
	}
}