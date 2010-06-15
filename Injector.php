<?php
require_once dirname(__FILE__) .'/Injector/Interface.php';
require_once dirname(__FILE__) .'/Locator/Interface.php';
require_once dirname(__FILE__) .'/Exception/NotBound.php';

/**
 * Wires Dependency Injector
 * Instantiates objects, injecting dependencies using the Wires_Locator
 *
 */
class Wires_Injector implements Wires_Injector_Interface
{
	/**
	 * Constructor
	 *
	 * @param Wires_Locator_Interface $locator
	 */
	function __construct(Wires_Locator_Interface $locator)
	{
		$this->locator = $locator;
	}

	/**
	 * Get an instance of $class with dependencies injected
	 *
	 * @param string $class
	 * @param string $context
	 * @return mixed
	 */
	function create($class, $context = Wires_Locator_Interface::GLOBAL_CONTEXT)
	{
		if (!($obj = $this->getBinding($class, $context)))	{
			throw new Wires_Exception_NotBound($class, $context);
		} elseif (is_object($obj)) {
			return $obj;
		}

		$ref_class = new ReflectionClass($obj);

		// if class has a constructor, fill in args
		if ($ref_method = $ref_class->getConstructor())	{
			return $ref_class->newInstanceArgs($this->getConstructorArgs($ref_method->getParameters(), $ref_class));

  		// if class is not instantiable, get a bound class that is
		} elseif (!$ref_class->isInstantiable()) {
			return $this->create($ref_class->getName(), $context);

  		// class is instantiable and has no constructor, just instantiate
		} else {
			return $ref_class->newInstance();
		}
	}

	/**
	 * Create a new injector with additional bindings (overriding previously defined ones)
	 *
	 * @param  $bindings array
	 * @return Wires_Injector
	 */
	function with(array $bindings)
	{
		$locator = clone($this->locator);
		$locator->loadBindings($bindings, true);

		return new Wires_Injector($locator);
	}

	/**
	 * Get a hierarchy of contexts for the given class as an array
	 *
	 * @param string $class
	 * @return array
	 */
	private function getContexts($class)
	{
		if ($class == Wires_Locator_Interface::GLOBAL_CONTEXT) {	
			return array($class);
		}

		$ref = new ReflectionClass($class);
		$contexts = array($class);

		// collect interfaces
		if (is_array($ref->getInterfaceNames())) {	
			foreach ($ref->getInterfaceNames() as $interface) {	
				$contexts[] = $interface;
			}
		}

		// add parent class
		if ($ref->getParentClass()) {
			$parent_contexts = $this->getContexts($ref->getParentClass()->getName());

			foreach ($parent_contexts as $pc) {	
				if ($pc != Wires_Locator_Interface::GLOBAL_CONTEXT && !in_array($pc, $contexts)) {	
					$contexts[] = $pc;
				}
			}
		}

		$contexts[] = Wires_Locator_Interface::GLOBAL_CONTEXT;

		return $contexts;
	}

	/**
	 * Gets an instance $class or the name of the class to instantiate for $class
	 * in the context hierarchy startng at $context
	 *
	 * @param string $class
	 * @param string $context
	 * @return mixed
	 */
	private function getBinding($class, $context = Wires_Locator_Interface::GLOBAL_CONTEXT)
	{
		$ref = new ReflectionClass($class);
		$contexts = $this->getContexts($context);

		foreach ($contexts as $context) {	

			// first check for bound singleton
			if ($this->locator->boundSingleton($class, $context))	{

				// if singleton already instantiated grab ref to it
				if ($this->locator->boundInstance($class, $context)) {
					$obj = $this->locator->getInstance($class, $context);

					// otherwise instantiate singleton					
				} else {
					$obj = $this->create($this->locator->getBinding($class, $context), $context);
					$this->locator->bindInstance($class, $obj, $context);
				}

				return $obj;
			}

			// get bound class
			if ($bound_class = $this->locator->getBinding($class, $context)) {	
				return $bound_class;
			}
		}

		// if $class is itself instantiable, return that
		if ($ref->isInstantiable()) {	
			return $class;
		}

		return false;
	}

	/**
	 * Get a bound value by name in the context hierarchy starting at $context
	 *
	 * @param string $name
	 * @param string $context
	 * @return mixed
	 */
	private function getBoundValue($name, $context = Wires_Locator_Interface::GLOBAL_CONTEXT)
	{
		$contexts = $this->getContexts($context);

		// search contexts until we find a value
		foreach ($contexts as $context) {	
			if ($this->locator->boundValue($name, $context)) {	
				return $this->locator->getValue($name, $context);
			}
		}

		return false;
	}

	/**
	 * Get a bound class by name in the context hierarchy starting at $context
	 *
	 * @param string $name
	 * @param string $context
	 * @return mixed
	 */
	private function getBoundClass($name, $context = Wires_Locator_Interface::GLOBAL_CONTEXT)
	{
		$contexts = $this->getContexts($context);

		// search contexts until we find a class name
		foreach ($contexts as $context) {
			if ($this->locator->boundClass($name, $context)) {
				return $this->locator->getClass($name, $context);
			}
		}

		return false;
	}

	/**
	 * Get a value for each constructor param for $ref_class
	 *
	 * @param array $params
	 * @param string $ref_class 
	 * @return array
	 */
	private function getConstructorArgs($params, $ref_class)
	{
		$args = array();

		foreach ($params as $i => $param) {
			$param_class = ($param->getClass() ? $param->getClass()->getName() : null);

			// param has class type hint
			if ($param_class)	{
				$args[] = $this->create($param_class, $ref_class->getName());

				// class bound to param name
			} elseif ($param_class = $this->getBoundClass($param->getName(), $ref_class->getName())) {
				$args[] = $this->create($param_class, $ref_class->getName());

				// value bound to param name
			} elseif ($this->getBoundValue($param->getName(), $ref_class->getName())) {
				$args[] = $this->getBoundValue($param->getName(), $ref_class->getName());

				// couldn't figure it out, set param to null
				// @TODO: warning or exception here?
			} else {
				$args[] = null;
			}
		}

		return $args;
	}


	/** @var Wires_Locator_Interface */
	private $locator;
}
