<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * The config file of an application.
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 * @abstract
 */
abstract class AppConfig {
	
	/**
	 * This function has to return the value of a specific constant or null if the constant is not defined.
	 * If a value is needed, and there is no value, it should return an exception.
	 * 
	 * @param String $pVal
	 * 			The name of the constant
	 * 
	 * @return int|boolean|string
	 */
	
	public function getConst($pVal) {
		$class = new \ReflectionClass($this);
		
		if (key_exists($pVal, $class->getConstants()))
			return $class->getConstant($pVal);
		
		//TODO: set an error code?
		throw new \RuntimeException("Constant [" . $pVal . "] is not defined.");
	}
}

?>