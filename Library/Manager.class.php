<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * Class Manager.
 * 
 * This class defines all the different methods that are needed to work when creating a manager.
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 * @abstract
 */
abstract class Manager{
	/**
	 * A specific DAO that will be used to get the informations from the server.
	 * @var DAO
	 */
	protected $dao;
	
	/**
	 * The name of the API of the DAO
	 * @var string
	 */
	protected $api;
	
	/**
	 * The module for which the manager is or null if it is a general manager
	 * @var null|string
	 */
	protected $module;
	
	/**
	 * The model for which the manager is
	 * @var string
	 */
	protected $model;
	
	/**
	 * ID of the manager, the value of the counter when the Manager is created
	 * @var int
	 */
	protected $id = 0;
	
	/**
	 * Number of manager that have been created
	 * @var int
	 */
	static protected $counter = 0;
	
	/**
	 * Constructor of the manager.
	 * Constructor that sets all the needed data. With this constructor, we are sure that the minimum data is provided.
	 * 
	 * @param DAO $dao
	 * @param string $api
	 * @param string|null $module
	 * @param string $model
	 */
	public function __construct($dao, $api, $module, $model){
		$this->dao = $dao;
		$this->api = $api;
		
		$this->module = $module;
		$this->model = $model;
		
		self::$counter++;
		
		$this->id = self::$counter;
	}
	
	/**
	 * Getter of the module
	 * @return string|null
	 */
	public function getModule() {
		return $this->module;
	}
	
	/**
	 * Getter of the model
	 * @return string
	 */
	public function getModel() {
		return $this->model;
	}
	
	/**
	 * Function that returns a {@see \Library\Entity} given its id
	 * 
	 * This function returns the only {@see \Library\Entity} that has a specific ID given in parameter.
	 * 
	 * @param int $pId
	 * @return \Library\Entity
	 * 
	 * @abstract
	 */
	abstract function get($pId);
	
	/**
	 * Function that returns a list of {@see \Library\Entity}
	 * 
	 * This function returns the list of {@see \Library\Entity} that respects all the different conditions that are provided. If no condition is provided, all the {@see \Library\Entity} of the server is returned
	 * 
	 * @param array $conditions
	 * 		A list of valid SQL conditions
	 * 
	 * @return \Library\Entity[]
	 * 
	 * @abstract
	 */
	abstract function getList(array $conditions = array(), array $param = array());


	/**
	 * Method that checks if we need to insert or to update
	 * 
	 * Check whether the {@see \Library\Entity} has already an ID or not. If yes, then it should update all the information on the server. If not, then it should insert the informations.
	 *
	 * @param \Library\Entity $pEntity
	 *
	 * @return int
	 * 		A positif value if the data has been well inserted/updated, negative oserwise
	 *
	 * @abstract
	 */
	abstract function send(\Library\Entity $pEntity);


	/**
	 * Function that inserts some new value
	 * 
	 * Inserts all the different informations from the {@see \Library\Entity} inside the DAO.
	 *
	 * @param \Library\Entity $pEntity
	 *
	 * @return int
	 * 		A positive value if the data has been well inserted, negative oserwise
	 *
	 * @abstract
	 */
	abstract function insert(\Library\Entity $pEntity);


	/**
	 * Function that updates some value
	 * 
	 * Update all the different informations from the {@see \Library\Entity} inside of the DAO, given his ID.
	 *
	 * @param \Library\Entity $pEntity
	 *
	 * @return int
	 * 		A positive value if the data has been well updated, negative oserwise
	 *
	 * @abstract
	 */
	abstract function update(\Library\Entity $pEntity);
	
	/**
	 * Function that deletes a {@see \Library\Entity} given its id
	 * 
	 * This function deletes all the information about a specific {@see \Library\Entity} given the ID of this Entity in parameter
	 * 
	 * @param int $pId
	 * @return bool
	 * 
	 * @abstract
	 */
	abstract function delete($pId);
	
	/**
	 * Function that deletes a list of {@see \Library\Entity}
	 * 
	 * This function deletes all the information about a list of {@see \Library\Entity} that respects all the conditions given in parameter.
	 * 
	 * @param array $conditions
	 * 			The list of conditions formated has valid SQL string condition
	 * @return bool
	 * 			Whether the deletation of the information has worked or not.
	 * 
	 * @abstract
	 */
	abstract function deleteList(array $conditions = array());
	
	/**
	 * Function used to return the specific DAO of the manager.
	 * 
	 * Has to be overrided by children and specify which type of DAO is about
	 */
	abstract protected function dao();
}

?>