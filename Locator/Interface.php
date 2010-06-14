<?php

interface Wires_Locator_Interface
{
	const GLOBAL_CONTEXT = '_global';

   function getInstance($abstract, $context = self::GLOBAL_CONTEXT);
	function getBinding($abstract, $context = self::GLOBAL_CONTEXT);
	function getValue($name, $context = self::GLOBAL_CONTEXT);
   function getClass($name, $context = self::GLOBAL_CONTEXT);
	function boundSingleton($abstract, $context = self::GLOBAL_CONTEXT);
	function boundValue($name, $context = self::GLOBAL_CONTEXT);
   function boundClass($name, $context = self::GLOBAL_CONTEXT);
}
