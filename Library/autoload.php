<?php

if (!defined("EVE_APP"))
	exit();
	
define("ERROR100", "Error 100: Tried to access inaccessible class [%s].");
define("ERROR101", "Error 101: Tried to access inaccessible class [%s].");

/**
 * Function that has to automatically load the instanciated objects that are not yet
 * loaded.
 * The file has to be of the form [className].class.php.
 * The method will replace all the . given by the namespace by some \ that are used in file architecture.
 * The namespace needs to give the link starting from root.
 * 
 * If the class is in Settings, it can be given as if the data was in the root folder.
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
		if (\Library\Application::logger() instanceof \Library\Logger)
			throw new \RuntimeException(\Library\Application::logger()->log("Error", "autoload", sprintf(ERROR100, $src), __FILE__, __LINE__));
		else
			throw new \RuntimeException(sprintf(ERROR101, $src));
	}
}
spl_autoload_register('autoload');

?>