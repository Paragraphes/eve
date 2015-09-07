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
class PDOFactory{
	/**
	 * Static method that gives a new connection on the DB using the PDO API. It checks where the user is (local or on the Internet) to choose the good login.
	 * 
	 * @throws \RuntimeException
	 * 			If the current information doesn't allow the BDD connection
	 * @return \PDO
	 */
	public static function getMysqlConnexion(){
		try{
			
			$db = new \PDO('mysql:host=' . \Library\Application::appConfig()->getConst("BDD_HOST")
							. ';dbname=' . \Library\Application::appConfig()->getConst("BDD_NAME"). ''
							, \Library\Application::appConfig()->getConst("BDD_USER")
							, \Library\Application::appConfig()->getConst("BDD_PASSWORD"));
			
			$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		} catch(\Exception $e) {
			if (\Library\Application::appConfig()->getConst("LOG"))
				throw new \RuntimeException("Error ID: " . \Library\Application::logger()->log("Error", "Page", "Error on BDD connection", __FILE__, __LINE__));
			else
				throw new \RuntimeException("Error on BDD connection");
			exit();
		}
		return $db;
	}
}

?>