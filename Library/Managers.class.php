<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * Class Managers.
 * 
 * This class is a factory to create the manager we need and avoid memory usage by creating again and agin the same {@see \Library\Manager}.
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class Managers{
	
	/**
	 * The name of the api wanted in this Managers
	 * @var string
	 */
	protected $api = NULL;
	
	/**
	 * The list of DAO given a specific API
	 * @var DAO[]
	 * @static
	 */
	protected static $dao = array();
	
	/**
	 * The name of the current module, null if the module is a general module
	 * @var unknown
	 */
	protected $module = "";
	
	/**
	 * The list of {@see \Library\Manager} that it controls
	 * @var {@see \Library\Manager}[]
	 */
	protected $managers = array();
	
	/**
	 * Constructor of the managers.
	 * The constructor set the different values and add the DAO on the list of DAO.
	 * 
	 * @param string $api
	 * @param string $dao
	 * @param string $module
	 */
	public function __construct($api, $dao, $module){
		$this->api = $api;
		self::$dao[$api] = $dao;
		$this->module = $module;
	}
	
	/**
	 * Function that gives back a {@see \Library\Manager}
	 * Given the model, this function will create a Manager for this model respecting the API of the {@see \Library\Managers}
	 * 
	 * Given a specific model, the {@see \Library\Managers} will check whether a model with the same name has already be created or not. If no, then it creates a new manager with all the different informations needed. Then it adds it in the {@see \Library\Manager} list and return the element of the list that correspond.
	 * 
	 * @param string $model
	 * 			The module to load
	 * 
	 * @throws \InvalidArgumentException
	 * 			If no model has been given or the model is not valid
	 * 
	 * @return \Library\Manager
	 */
	public function getManagersOf($model){
		
		if(!is_string($model) || empty($model))
			if (\Library\Application::appConfig()->getConst("LOG"))
				throw new \InvalidArgumentException("Error ID: " . \Library\Application::logger()->log("Error", "Manager_PDO", "Le model spécifié est invalide", __FILE__, __LINE__));
			else
				throw new \InvalidArgumentException("Le model spécifié est invalide");
		
		if(!isset($this->managers[$model])){
			if (file_exists("./Modules/" . $this->module . "/Models/" . $model . "Manager.class.php")) {
				$module = $this->module;
				$manager = "\\Modules\\" . $this->module . "\\Models\\"; 
			} elseif (file_exists("./Library/Models/" . $model . "Manager.class.php")) {
				$manager = "\\Library\\Models\\";
				$module = null;
			} else {
				if (\Library\Application::appConfig()->getConst("LOG"))
					throw new \InvalidArgumentException("Error ID: " . \Library\Application::logger()->log("Error", "Manager_PDO", "Le model spécifié est invalide", __FILE__, __LINE__));
				else
					throw new \InvalidArgumentException("Le model spécifié est invalide");
			}
			
			$manager .= $model . 'Manager_' . $this->api;
			
			$this->manager[$model] = new $manager(self::$dao[$this->api], $this->api, $module, $model);
		}
		
		return $this->manager[$model];
	}
	
	/**
	 * Function that returns a DAO
	 * 
	 * A function that returns a specific DAO given an API from the list of DAO.
	 * 
	 * @param string $api
	 * 
	 * @throws \RuntimeException
	 * 			If the API doesn't contains any DAO
	 * 
	 * @return DAO
	 */
	public static function getDao($api) {
		
		if (!key_exists($api, self::$dao))
			if (\Library\Application::appConfig()->getConst("LOG"))
				throw new \RuntimeException("Error ID: " . \Library\Application::logger()->log("Error", "Manager_PDO", "Try to get undefined DAO API: [" . $api . "]", __FILE__, __LINE__));
			else
				throw new \RuntimeException("Try to get undefined DAO API: [" . $api . "]");
		
		return self::$dao[$api];
	}
	
}

?>