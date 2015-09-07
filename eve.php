<?php

error_reporting(E_ALL);

date_default_timezone_set('Europe/Zurich');

define("EVE_APP", true);

require 'Library/autoload.php';
//require 'Library/errorHandler.php';

try {
	//$app = new Applications\Eve\EveApplication(__DIR__);
	$app = new Applications\Eve\EveApplication('/var/www/html/eve');
	$app->run();
} catch (Exception $e) {
	echo "An error has occured, please check the logs for more detail.<br>" . $e->getMessage();
	//TODO: decide what else to do upon errors?
}

?>