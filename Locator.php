<?php
require_once dirname(__FILE__) .'/Locator/Interface.php';
require_once dirname(__FILE__) .'/Exception/AlreadyBound.php';
require_once dirname(__FILE__) .'/Exception/InvalidBinding.php';
require_once dirname(__FILE__) .'/Exception/NotBound.php';

/**
 * Locator to maintain dependency binding definitions
 */
class Wires_Locator implements Wires_Locator_Interface
{
	/**
	 * Constructor
	 *
	 * @param array|null $bindings
	 */
	function __construct(array $bindings = null)
   {
		if (!is_null($bindings)) {	
			$this->loadBindings($bindings);
		}
	}

	/**
	 * Load an array of binding definitions
	 * 
	 * @param array $bindings
	 * @param boolean $overwrite
	 */
	function loadBindings(array $bindings, $overwrite = false)
   {	
		foreach ($bindings as $context => $classes) {	
			foreach ($classes as $abstract => $concrete)	{	
				if (is_array($concrete)) {	
					if (isset($concrete['asSingleton']) && $concrete['asSingleton']) {	
						$this->bindAsSingleton($abstract, $concrete['class'], $context, $overwrite);
					} elseif (isset($concrete['value']))	{	
						$this->bindValue($abstract, $concrete['value'], $context, $overwrite);
               } elseif (isset($concrete['class'])) {
                  $this->bindClass($abstract, $concrete['class'], $context, $overwrite);
					} else {
                  throw new Wires_Exception_InvalidBinding($abstract, $concrete);
               }
				} else {	
					$this->bind($abstract, $concrete, $context, $overwrite);
				}
			}
		}
	}

	/**
	 * Binds an abstract interface name to a concrete class name within a context.
	 *
	 * @param string $abstract
	 * @param string $concrete
	 * @param string $context
	 * @param boolean $overwrite
	 * @return Wires_Locator
	 */
	function bind($abstract, $concrete, $context = self::GLOBAL_CONTEXT, $overwrite = false)
   {
		if (!$overwrite && isset($this->bindings[$context][$abstract])) {
			throw new Wires_Exception_AlreadyBound($abstract, $concrete, $context, $this->bindings[$context][$abstract]);
		}

		$this->bindings[$context][$abstract] = $concrete;

		return $this;
	}

   /**
    * Binds a class name to a parameter name
    *
    * @param string $name
    * @param string $class
    * @param string $context
    * @param boolean $overwrite
	 * @return Wires_Locator
    */
   function bindClass($name, $class, $context = self::GLOBAL_CONTEXT, $overwrite = false)
   {
      if (!$overwrite && isset($this->classes[$context][$name])) {
         throw new Wires_Exception_AlreadyBound($name, $class, $context, $this->classes[$context][$name]);
      }

      $this->classes[$context][$name] = $class;

		return $this;
   }

	/**
	 *	Bind an abstract interface to a class as a singleton
	 *
	 * @param string $abstract
	 * @param string $concrete
	 * @param string $context
	 * @return Locator
	 */
	function bindAsSingleton($abstract, $concrete, $context = self::GLOBAL_CONTEXT, $overwrite = false)
   {	
		if (!$overwrite && isset($this->singletons[$context][$abstract])) {
			throw new Wires_Exception_AlreadyBound($abstract, $concrete, $context, $this->singletons[$context][$abstract]);
		}

		$this->singletons[$context][$abstract] = $concrete;

		return $this;
	}

	/**
	 * Bind a value to an arg name in a context
	 *
	 * @param string $name
	 * @param mixed $value
	 * @param string $context
	 * @return Locator
	 */
	function bindValue($name, $value, $context = self::GLOBAL_CONTEXT, $overwrite = false)
   {
		if (!$overwrite && isset($this->values[$context][$name])) {
			throw new Wires_Exception_AlreadyBound($name, $value, $context, $this->values[$context][$name]);
		}

		$this->values[$context][$name] = $value;

		return $this;
	}

	/**
	 * Bind an existing instance as a singleton
	 *
	 * @param string $abstract
	 * @param mixed $instance
	 * @param string $context
	 * @return Locator
	 */
	function bindInstance($abstract, $instance, $context = self::GLOBAL_CONTEXT, $overwrite = false)
   {
		if (!$overwrite && isset($this->instances[$context][$abstract])) {
			throw new Wires_Exception_AlreadyBound($abstract, 'an instance', $context, 'another instance');
		}

		$this->singletons[$context][$abstract] = get_class($instance);
		$this->instances[$context][$abstract] = $instance;

		return $this;
	}

	/**
	 * Get the name of the class bound to $abstract. Looks in singleton bindings first
	 * then singleton bindings
	 * 
	 * @param string $abstract
	 * @param string $context
	 * @return string
	 */
	function getBinding($abstract, $context = self::GLOBAL_CONTEXT)
   {
   	if (isset($this->singletons[$context][$abstract])) {	
			return $this->singletons[$context][$abstract];
		}
		
   	if (isset($this->bindings[$context][$abstract])) {	
			return $this->bindings[$context][$abstract];
		}

		return false;
	}

	/**
	 * Get a primitive bound to a class context
	 *
	 * @param string $name
	 * @param string $context
	 * @return mixed
	 */
	function getValue($name, $context = self::GLOBAL_CONTEXT)
   {
		if (!($this->boundValue($name, $context))) {	
			throw new Wires_Exception_NotBound($name, $context);
		}

		return $this->values[$context][$name];
	}

   /**
    * Get the class name bound to a parameter name
    *
    * @param string $name
    * @param string $context
    * @return string
    */
   function getClass($name, $context = self::GLOBAL_CONTEXT)
   {
      if (!($this->boundClass($name, $context))) {
         throw new Wires_Exception_NotBound($name, $context);
      }

      return $this->classes[$context][$name];
   }

   /**
	 * Gets an instance of a bound class
	 *
	 * @param string $abstract
	 * @param string $context
	 * @return mixed
	 */
	function getInstance($abstract, $context = self::GLOBAL_CONTEXT)
   {	
		if (!isset($this->instances[$context][$abstract])) {
			throw new Wires_Exception_NotBound($abstract, $context);
		}

		return $this->instances[$context][$abstract];
	}

	/**
	 * Checks whether an abstract interface is bound, singleton or not
	 *
	 * @param string $abstract
	 * @param string $namespace
	 * @return boolean
	 */
	function bound($abstract, $context = self::GLOBAL_CONTEXT)
   {
		return (isset($this->bindings[$context][$abstract]) || isset($this->singletons[$context][$abstract]));
	}

	/**
	 * Checks whether an abstract interface is bound as a singleton
	 *
	 * @param string $abstract
	 * @param string $context
	 * @return boolean
	 */
	function boundSingleton($abstract, $context = self::GLOBAL_CONTEXT)
   {
		return (isset($this->singletons[$context][$abstract]));
	}
	
	/**
	 * Checks whether an abstract interface is bound to an instance
	 *
	 * @param string $abstract
	 * @param string $context
	 * @return boolean
	 */
	function boundInstance($abstract, $context = self::GLOBAL_CONTEXT)
   {
		return (isset($this->instances[$context][$abstract]));
	}

	/**
	 *	Checks whether a value is bound within a context
	 *
	 * @param string $name
	 * @param string $context
	 * @return boolean
	 */
	function boundValue($name, $context = self::GLOBAL_CONTEXT)
   {
		return (isset($this->values[$context][$name]));
	}

   /**
    * Checks whether a parameter name is bound to a class name
    *
    * @param string $name
    * @param string $context
    * @return boolean
    */
   function boundClass($name, $context = self::GLOBAL_CONTEXT)
   {
      return (isset($this->classes[$context][$name]));
   }

	private $bindings = array();
	private $singletons = array();
	private $values = array();
   private $classes = array();
	private $instances = array();
}
