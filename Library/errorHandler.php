<?php

if (!defined("EVE_APP"))
	exit();
/**
 * A method that catches the different errors sent by the system.
 * 
 * This method will only log the errors in a file if we want to log errors.
 * If not, the error handler will just continue.
 * 
 * @param unknown $errNo
 * 				ID of the error. Catch with the different constant.
 * @param unknown $errStr
 * 				Description of the error
 * @param unknown $errFile
 * 				File where the error has been detected
 * @param unknown $errLine
 * 				Error where the error has been detected
 * @return false
 * 				Say that we want the nativ error handler to continue checking the error 
 */
function errorHandler($errNo, $errStr, $errFile, $errLine){
	$config = \Library\Application::appConfig(); 
	
	if (!is_null($config) && $config->getConst("LOG")) {
		switch ($errNo) {
			case E_USER_ERROR:
				$type = "user_error";
	        	break;
			case E_USER_WARNING:
				$type = "user_warning";
				break;
			case E_USER_NOTICE:
				$type = "user_notice";
				break;
			default:
				$type = "unknow";
				break;
		}
		
		\Library\Application::logger()->log("ErrorHandler", $type, $errStr, $errFile, $errLine);
	}
	
	return false;
}

/**
 * Say which method should catch the error
 */
set_error_handler('errorHandler');

?>