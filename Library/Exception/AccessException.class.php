<?php

namespace Library\Exception;

if (!defined("EVE_APP"))
	exit();

/**
 * A class that checks all the exceptions conserning the access. It gives some informations on deconnection, time finished and so on.
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class AccessException extends \Exception {
	
	/**
	 * The login and password are not valid
	 * @var int
	 */
	const INVALID_LOGIN = 1;
	
	/**
	 * Past too much time whithout doing anything 
	 * @var int
	 */
	const TIME_FINISHED = 2;
	
	/**
	 * User try to deconnect
	 * @var int
	 */
	const DECONEXION = 3;
	
	/**
	 * No {@see \Library\Route} match the URI
	 * @var int
	 */
	const NO_ROAD = 4;


	/**
	 * User access lower than route access
	 * @var int
	 */
	const NOT_ALLOWED = 5;
}

?>