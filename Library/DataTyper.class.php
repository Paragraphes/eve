<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * A factory that has to create a specific {@see \DataTyper_Manager} depending on the DAO we want.
 *
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class DataTyper{
	
	/**
	 * The DataTyper's DAO was never set, meaning that {@see self::__construct()} was never called.
	 */
	const ERROR500 = "Error 500: Need to provide a DAO for DataTyper to work.";
	
	/**
	 * An instance of the {@see \Library\DataTyper_Manager} that uses the DAO we want.
	 * 
	 * @var \Library\DataTyper_Manager
	 * @static
	 */
	protected static $typer;
	
	/**
	 * The constructor of the factory.
	 * It creates an instance of {@see \Library\DataTyper_Manager} with the specific DAO that is passed on parameter. It avoids to change a lot of code when we want to change from a DAO to another. We only need to change the value on the factory creation. This part will create the static method that will be needed later.
	 * @see \Library\DataTyper_Manager
	 * 
	 * @param string $api
	 */
	public function __construct($api) {
		$typerManager = "\\Library\\DataTyper_" . $api;
		
		self::$typer = new $typerManager();
	}
	
	/**
	 * An interface between the application and the {@see \Library\DataTyper_Manager} It is created static, then we can call it from everywhere.
	 * 
	 * It should ask the specific {@see \Library\DataTyper_Manager} to give the type that we need.
	 * 
	 * @see \Library\DataTyper_Manager::getDataType()
	 * 
	 * @param string $module
	 * @param string $model
	 * 
	 * @static
	 * 
	 * @return string[]
	 *
	 * @throws \RuntimeException
	 * 			If the DAO has not been provided, that means that the {@see self::_constructor()} has never been called
	 */
	public static function getDataType($module, $model) {
		if (!isset(self::$typer))
			throw new \RuntimeException(\Library\Application::logger()->log("Error", "DataTyper", self::ERROR500, __FILE__, __LINE__), \Library\Exception\AccessException::DECONEXION);
		
		return self::$typer->getDataType($module, $model);
	}
}

?>