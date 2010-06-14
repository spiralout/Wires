<?php

interface Wires_Locator_Interface
{
	const GLOBAL_CONTEXT = '_global';

	function loadBindings(array $bindings, $overwrite = false);
	function boundSingleton($abstract, $context = self::GLOBAL_CONTEXT);
	function boundInstance($abstract, $context = self::GLOBAL_CONTEXT);
   function getInstance($abstract, $context = self::GLOBAL_CONTEXT);
   function getBinding($abstract, $context = self::GLOBAL_CONTEXT);
   function bindInstance($abstract, $instance, $context = self::GLOBAL_CONTEXT, $overwrite = false);
   function getValue($name, $context = self::GLOBAL_CONTEXT);
   function boundValue($name, $context = self::GLOBAL_CONTEXT);
   function getClass($name, $context = self::GLOBAL_CONTEXT);
	function boundClass($name, $context = self::GLOBAL_CONTEXT);
}
