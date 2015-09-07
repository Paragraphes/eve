<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();


/**
 * The configuration class.
 * 
 * This class gives all the configuration informations that the application may need and that is saved on the server.
 * This configuration informations are easier to change than the informations on the {@see \Library\AppConfig}
 *
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class Config {
	
	/**
	 * Manager of the configuration.
	 * It gives the interface between the data on the server and the application.
	 * 
	 * @var \Library\Manager
	 */
	protected $manager = NULL;
	
	/**
	 * Constructor of the configuration
	 * @param Manager $pManager
	 * 				An instance of the interface between server data and the application
	 */
	public function __construct(Manager $pManager) {
		$this->manager = $pManager;
	}
	
	/**
	 * Returns a value related to a specific key.
	 * 
	 * @param String $key
	 * 			The key that give the config value
	 * 
	 * @return NULL|string|boolean|int
	 */
	public function get($key) {
		
		$config = $this->manager->get(new \Library\Entities\config(array("clef" => $key)));
		
		if ($config != null) {
			return $config->valeur();
		} else {
			return null;
		}
		
	}
	
}

?>