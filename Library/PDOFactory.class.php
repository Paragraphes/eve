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
	const ERROR310 = "Error 310: Could not obtain PDO connection.";
	/**
	 * An error happened while trying to begin a transaction.
	 */
	const ERROR311 = "Error 301: Could not begin PDO transaction.";
	/**
	 * An error happened while trying to commit a transaction. Transaction was rolled back.
	 */
	const ERROR312 = "Error 302: Could not commit PDO transaction.";
	
	private static $instance = null;
	
	/**
	 * Static method that gives a new connection on the DB using the PDO API.
	 * It checks where the user is (local or on the Internet) to choose the right login.
	 * 
	 * @throws \RuntimeException
	 * 			If the current information doesn't allow the BDD connection
	 * @return \PDO
	 */
	public static function getConnexion() {
		if (self::$instance == null) {
			try{
				self::$instance = new \PDO('mysql:host=' . \Library\Application::appConfig()->getConst("BDD_HOST")
								. ';dbname=' . \Library\Application::appConfig()->getConst("BDD_NAME"). ''
								, \Library\Application::appConfig()->getConst("BDD_USER")
								, \Library\Application::appConfig()->getConst("BDD_PASSWORD"));
				
				self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			} catch(\Exception $e) {
				throw new \RuntimeException(\Library\Application::logger()->log("Error", "DatabaseConnection", self::ERROR310, __FILE__, __LINE__));
				exit();
			}
		}
		return self::$instance;
	}
	
	/**
	 * Static method that begins a transaction with the PDO API.
	 * 
	 * @throws \RuntimeException
	 * 			If an exception happens while trying to begin the transaction.
	 */
	public static function beginTransaction() {
		try {
			$instance->beginTransaction();
		} catch(\Exception $e) {
			throw new \RuntimeException(\Library\Application::logger()->log("Error", "DatabaseConnection", self::ERROR311, __FILE__, __LINE__));
		}
	}
	
	/**
	 * Static method that commits a transaction with the PDO API.
	 * If any exceptions occur while committing, the changes are rolled back.
	 * 
	 * @throws \RuntimeException
	 * 			If an exception happens while trying to commit the transaction.
	 */
	public static function commitTransaction() {
		try {
			$instance->commit();
		} catch(\Exception $e) {
			self::rollBack();
			throw new \RuntimeException(\Library\Application::logger()->log("Error", "DatabaseConnection", self::ERROR312, __FILE__, __LINE__));
		}
	}
	
	/**
	 * Static method that rolls  back a transaction with the PDO API.
	 */
	public static function rollBack() {
		$instance->rollBack();
	}
}

?>