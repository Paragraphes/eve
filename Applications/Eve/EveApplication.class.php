<?php

namespace Applications\Eve;

if (!defined("EVE_APP"))
	exit();

/*
*
* Application de la Plateforme de Publication
*
* @extends Application
*
*/
class EveApplication extends \Library\Application{
	
	// Crée une application nommée PlatPub
	public function __construct($root){
		$this->name = 'Eve';
		
		parent::__construct($root);
	}
	
	// Permet de faire fonctionner l'application
	public function run(){
		$controller = $this->getController();
		
		$controller->execute();
		$this->httpResponse->setPage($controller->page());
		$this->httpResponse->send();
	}
	
}

?>