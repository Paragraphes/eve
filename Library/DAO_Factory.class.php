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
 * @version 1.0
 */
class DAO_Factory {
	
	/**
	 * No database was found corresponding to the desired connection type.
	 */
	const ERROR300 = "Error 300: Could not obtain connection for given type.";
	const ERROR301 = "Error 301: Could not begin transaction for given type.";
	const ERROR302 = "Error 302: Could not end transaction for given type.";
	const ERROR303 = "Error 303: Could not commit transaction for given type.";
	
	/**
	 * The factory itself.
	 * It checks with a switch which DAO is needed and return the db connection
	 * corresponding to the DAO needed.
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
			throw new \RuntimeException(\Library\Application::logger()->log("Error", "DatabaseConnection", self::ERROR300, __FILE__, __LINE__));
	}
	
	public static function beginTransaction($type) {
		$cname = "\\Library\\" . $type . "Factory";
		
		if (class_exists($cname))
			return $cname::beginTransaction();
		else
			throw new \RuntimeException(\Library\Application::logger()->log("Error", "DatabaseConnection", self::ERROR301, __FILE__, __LINE__));
	}
	
	public static function endTransaction($type) {
		$cname = "\\Library\\" . $type . "Factory";
		
		if (class_exists($cname))
			return $cname::endTransaction();
		else
			throw new \RuntimeException(\Library\Application::logger()->log("Error", "DatabaseConnection", self::ERROR302, __FILE__, __LINE__));
	}
	
	public static function commitTransaction($type) {
		$cname = "\\Library\\" . $type . "Factory";
		
		if (class_exists($cname))
			return $cname::commitTransaction();
		else
			throw new \RuntimeException(\Library\Application::logger()->log("Error", "DatabaseConnection", self::ERROR303, __FILE__, __LINE__));
	}
}

?>