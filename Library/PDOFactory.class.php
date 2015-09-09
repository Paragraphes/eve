<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * Factory that generates a DAO for PDO
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class PDOFactory implements DAO_Interface {
	
	/**
	 * An error happened while trying to connect to the database.
	 */
	const ERROR310 = "Error 310: Error on DB connection.";
	
	/**
	 * Static method that gives a new connection on the DB using the PDO API.
	 * It checks where the user is (local or on the Internet) to choose the right login.
	 * 
	 * @throws \RuntimeException
	 * 			If the current information doesn't allow the BDD connection
	 * @return \PDO
	 */
	public static function getConnexion() {
		try{
			
			$db = new \PDO('mysql:host=' . \Library\Application::appConfig()->getConst("BDD_HOST")
							. ';dbname=' . \Library\Application::appConfig()->getConst("BDD_NAME"). ''
							, \Library\Application::appConfig()->getConst("BDD_USER")
							, \Library\Application::appConfig()->getConst("BDD_PASSWORD"));
			
			$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		} catch(\Exception $e) {
			throw new \RuntimeException(\Library\Application::logger()->log("Error", "DatabaseConnection", self::ERROR310, __FILE__, __LINE__));
			exit();
		}
		return $db;
	}
	
	public static function beginTransaction() {
		
	}
	
	public static function endTransaction() {
		
	}
	
	public static function commitTransaction() {
		
	}
}

?>