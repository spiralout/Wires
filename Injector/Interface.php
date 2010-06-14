<?php

interface Wires_Injector_Interface
{
	function create($class, $context = null);
   function with(array $bindings);
}
