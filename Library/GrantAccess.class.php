<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * Class that checks if an user is allowed to continue on the website or not.
 * The class has to check if an user has been inactive for too long.
 * This mesure is used to protect the user and avoid that someone else uses the current session.
 * It also avoids that someone steals the session id since we check the session id and the IP.
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 * @abstract
 */
abstract class GrantAccess {
	
	/**
	 * The given DAO is an empty string or non-string argument.
	 */
	const ERROR800 = "Error 800: DAO has to be a valid string.";
	/**
	 * No subclass of GrantAccess was found for the given DAO.
	 */
	const ERROR810 = "Error 810: Trying to use illegal DAO [%s].";
	
	/**
	 * A factory to create a specific GrantAccess function of the choosen DAO.
	 * 
	 * It'll check if the DAO is valid and return a subclass instance of {@see \Library\GrantAccess}
	 * 
	 * @param string $dao
	 * @param \Library\Route $route
	 * @param int $user
	 * @throws \RuntimeException
	 * 			Throws an exception if the DAO is not usable for GrantAccess or if the result is not a subclass of {@see \Library\GrantAccess}
	 * 
	 * @return \Library\GrantAccess
	 */
	public static function grantAccess($dao, $route, $user) {
		if (empty($dao) || !is_string($dao))
			throw new \RuntimeException(\Library\Application::logger()->log("Error", "AccessError", self::ERROR800, __FILE__, __LINE__));
		
		$className = "\\Library\\GrantAccess_" . $dao;
		$class = new $className();
		
		if (!$class instanceof \Library\GrantAccess || is_null($class))
			throw new \RuntimeException(\Library\Application::logger()->log("Error", "AccessError", sprintf(self::ERROR810, $dao), __FILE__, __LINE__));
		
		return $class->checkAccess($route, $user);
	}
	
	/**
	 * Method to check the user autorisation
	 * 
	 * This method has to use the DAO provided before to check whether or not an user is allowed to continue on the authenticated part of the website.
	 * 
	 * This method has to check different attributes
	 *
	 * - The user doesn't try to deconnect
	 * - If the user is not defined, it means that the user is not connected and the user is allowed to continue.
	 * - one has to check if the user has changed his page in a defined time. This time has to be defined in the application configuration.
	 * - And update the timer to set that the user is still connected
	 * 
	 * This solution protects against session id stealing and protect an user who let his computer with active session.
	 * 
	 * @param int $pRoute
	 * @param int $pUser
	 * 
	 * @return boolean
	 * 
	 * @abstract
	 */
	abstract public function checkAccess($pRoute, $pUser);
	
}

?>