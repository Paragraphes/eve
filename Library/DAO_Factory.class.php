<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * A factory to get the connection.
 * 
 * A factory using the Design Pattern Factory to generates the DAO we need.
 *
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @author Toudoudou
 * @version 1.0
 */
class DAO_Factory {
	
	/**
	 * No database was found corresponding to the given connection type.
	 */
	const ERROR300 = "Error 300: Could not obtain connection for given type [%s].";
	/**
	 * The beginTransaction method was not found on the given connection type.
	 */
	const ERROR301 = "Error 301: Could not begin transaction for given type [%s].";
	/**
	 * The commitTransaction method was not found on the given connection type.
	 */
	const ERROR302 = "Error 302: Could not commit transaction for given type [%s].";
	/**
	 * The rollBack method was not found on the given connection type.
	 */
	const ERROR303 = "Error 303: Could not roll back transaction for given type [%s].";
	
	/**
	 * The factory itself.
	 * It returns a DB connection corresponding to the given DAO type.
	 * 
	 * @param string $type
	 * 			The type of the DB.
	 * @throws \RuntimeException
	 * 			If the type of the DB is unknown to the system
	 * @return mixed
	 * 			A connection to a specific DB (or anything else)
	 * @static
	 */
	public static function getConnexion($type) {
		$cname = "\\Library\\" . $type . "Factory";
		
		if (class_exists($cname))
			return $cname::getConnexion();
		else
			throw new \RuntimeException(\Library\Application::logger()->log("Error", "DatabaseConnection", sprintf(self::ERROR300, $type), __FILE__, __LINE__));
	}
	
	/**
	 * Begins a transaction corresponding to the given DAO type.
	 * 
	 * @param string $type
	 * 		The type of the DB.
	 * @throws \RuntimeException
	 * 		If the DB type is unknown to the system
	 */
	public static function beginTransaction($type) {
		$cname = "\\Library\\" . $type . "Factory";
		
		if (class_exists($cname))
			return $cname::beginTransaction();
		else
			throw new \RuntimeException(\Library\Application::logger()->log("Error", "DatabaseConnection", sprintf(self::ERROR301, $type), __FILE__, __LINE__));
	}
	
	/**
	 * Commits a transaction corresponding to the given DAO type.
	 *
	 * @param string $type
	 * 		The type of the DB.
	 * @throws \RuntimeException
	 * 		If the DB type is unknown to the system
	 */
	public static function commitTransaction($type) {
		$cname = "\\Library\\" . $type . "Factory";
		
		if (class_exists($cname))
			return $cname::commitTransaction();
		else
			throw new \RuntimeException(\Library\Application::logger()->log("Error", "DatabaseConnection", sprintf(self::ERROR302, $type), __FILE__, __LINE__));
	}
	
	/**
	 * Rolls back a transaction corresponding to the given DAO type.
	 *
	 * @param string $type
	 * 		The type of the DB.
	 * @throws \RuntimeException
	 * 		If the DB type is unknown to the system
	 */
	public static function rollBack($type) {
		$cname = "\\Library\\" . $type . "Factory";
		
		if(class_exists($cname))
			return $cname::rollBack();
		else
			throw new \RuntimeException(\Library\Application::logger()->log("Error", "DatabaseConnection", sprintf(self::ERROR303, $type), __FILE__, __LINE__));
	}
}

?>