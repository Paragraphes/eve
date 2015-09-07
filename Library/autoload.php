<?php

if (!defined("EVE_APP"))
	exit();
/**
 * Function that has to automaticaly load the instanciated object that are not yet
 * loaded.
 * The file has to be of the forme [className].class.php.
 * The method will replace all the . given by the namespace by some \ that are used in file architecture.
 * The namespace need to give the link from root.
 * 
 * If the class is in Settings, it could be given like if the data is in the root folder.
 * 
 * @param string $class
 * @throws \RuntimeException
 * 			If it is not possible to find the class
 */
function autoload($class){
		
	$src = str_replace('.', '/', $class) . '.class.php';
	$src = str_replace('\\', '/', $src);
		
	if(is_file($src)){
		require($src);
	} elseif (is_file("Settings/" . $src)) {
		require("Settings/" . $src);
	} else {
		
		if (\Library\Application::appConfig()->getConst("LOG"))
			throw new \RuntimeException("Error ID: " . \Library\Application::logger()->log("Error", "autoload", "Try to access on unaccesible class [" . $class . "]", __FILE__, __LINE__));
		else
			throw new \RuntimeException("Try to access on unaccesible class [" . $class . "]");
	}
}
spl_autoload_register('autoload');

?>