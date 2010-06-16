<?php
require_once dirname(__FILE__) .'/../Exception.php';

class Wires_Exception_AlreadyBound extends Wires_Exception
{
   function __construct($abstract, $concrete, $context, $existing)
   {
      parent::__construct("Cannot bind {$abstract} to {$concrete} in context {$context}. Already bound to {$existing}");
   }
}
