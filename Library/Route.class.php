<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * Class that represents one of the different links possible on the application.
 * 
 * It is a kind of Entity.
 *
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class Route{
	
	/**
	 * The ID of the route
	 * 
	 * @var int
	 */
	protected $id;
	
	/**
	 * The view that we want for this road
	 * 
	 * @var string
	 */
	protected $action;
	
	/**
	 * The module on which the view and the controller are for this road
	 * 
	 * @var string
	 */
	protected $module;
	
	/**
	 * A regular expression that represent the road
	 * 
	 * @var String
	 */
	protected $url;
	
	/**
	 * The key of the different variables linked to this road. The different keys are separeted by coma
	 * 
	 * @var string
	 */
	protected $vars;
	
	/**
	 * List with key=>value returned when the URI has ben compared with the URI of the roads, from first to last, and the different values have been returned
	 * The comparition stops when the correct road has been found
	 * 
	 * @var mixed[]
	 */
	protected $varsListe = array();
	
	/**
	 * Level of administration needed to go on this page
	 * 
	 * @var int
	 */
	protected $admin_lvl;
	
	/**
	 * Constructor of the road.
	 * Sets automatically all the different informations about the road
	 * 
	 * @param mixed[] $options
	 */
	public function __construct(array $options = array()){
		if(!empty($options)){
			$this->hydrate($options);
		}
	}
	
	/**
	 * Function that checks whether there exists a setter for the different keys provided in the array in parameter or not and sets the different attribute with the value
	 * 
	 * @param mixed[] $option
	 */
	public function hydrate(array $option){
		
		foreach($option AS $key=>$value){
			$method = 'set' . ucfirst($key);
			if(is_callable(array($this, $method))){
				$this->$method($value);
			}
		}
	}
	
	
	/**
	 * Returns whether there is variable for this road or not
	 * 
	 * @return boolean
	 */
	public function hasVars(){
		return !empty($this->vars);
	}
	
	/**
	* Checks if the parameter matches with the url of the road. If there is no variable and the parameter and the road are the same, then this road matches. If the patern matches with the URI provided in parameter, then this road matches. Else the road doesn't match
	*
	* @param string $url
	* 			The uri to test
	*
	* @return false|string[]
	* 				The table with the value of all the different variables if the road matches. False if the road doesn't match.
	*
	*/
	public function matchUrl($url){
		$aUrl = explode('?', $url);
		$aUrl = explode('#', $aUrl[0]);
		$url = $aUrl[0];
		
		if ($this->hasVars() && $this->url == $url)
			return array(); 
		
		if(preg_match('`^' . $this->url . '$`', $url, $matches)){
			unset($matches[0]);
			return $matches;
		}else{
			return false;
		}
	}
	
	/**
	 * Setter of the action
	 * 
	 * @param string $action
	 */
	public function setAction($action){
		if(is_string($action)){
			$this->action = $action;
		}
	}
	
	/**
	 * Setter of the ID
	 * 
	 * @param int $pVal
	 * @throws \IllegalArgumentException
	 * 			If the ID is not numeric
	 */
	public function setId($pVal){
		if(!is_numeric($pVal) || empty($pVal))
				if (\Library\Application::appConfig()->getConst("LOG"))
					throw new \IllegalArgumentException("Error ID: " . \Library\Application::logger()->log("Error", "Route", "L'identifiant doit être un nombre.", __FILE__, __LINE__));
				else
					throw new \IllegalArgumentException("L'identifiant doit être un nombre.");
				
		$this->id = $pVal;
	}
	
	/**
	 * Setter of the url
	 * 
	 * @param string $module
	 */
	public function setModule($module){
		if(is_string($module)){
			$this->module = $module;
		}
	}
	
	/**
	 * Setter of the url
	 * 
	 * @param string $url
	 */
	public function setUrl($url){
		if(is_string($url)){
			$this->url = $url;
		}
	}
	
	/**
	 * Setter of the varsListe.
	 * Replace the current varsListe by the new value in parameter
	 * 
	 * @param string[] $varsListe
	 */
	public function setVarsListe(array $varsListe){
		$this->varsListe = $varsListe;
	}
	
	/**
	 * Setter for vars
	 * 
	 * @param string $vars
	 */
	public function setVars(array $vars){
		$this->vars = $vars;
	}
	
	/**
	 * Method to add one var at the end of the varsListe. 
	 * Add a new variable specified by his key and his value at the end of the variable list
	 * 
	 * If the key already exists, it can't replace id since force is not set to 1 to force the replacement. By default the value is not replaced.
	 * 
	 * It is allowed to add key that are not in the vars
	 * 
	 * @param string $key
	 * @param mixed $val
	 * @param number $force
	 * 
	 * @return number
	 */
	public function addVarInListe($key, $val, $force = 0) {
		if (key_exists($key, $this->varsListe) && $force == 0) {
			trigger_error("Value already exist, impossible to replace whitout force");
			return 0;
		}
		
		$this->varsListe[$key] = $val;
		return 1;
	}
	
	/**
	 * getter of id
	 * 
	 * @return int
	 */
	public function id(){
		return $this->id;
	}
	
	/**
	 * getter of action
	 * 
	 * @return String
	 */
	public function action(){
		return $this->action;
	}
	
	/**
	 * getter of module
	 * 
	 * @return String
	 */
	public function module(){
		return $this->module;
	}
	
	/**
	 * getter of url
	 * 
	 * @return String
	 */
	public function url(){
		return $this->url;
	}
	
	/**
	 * getter of varsListe
	 * 
	 * @return mixed[]
	 */
	public function varsListe(){
		return $this->varsListe;
	}
	
	/**
	 * getter of the keys.
	 * The keys are transformed into an array by exploding on the coma
	 * 
	 * @return string[]
	 */
	public function vars(){
		
		return array_map(function($arg) {
			return trim($arg);
		}, explode(',', $this->vars));
	}
	
	/**
	 * getter of admin_lvl
	 * 
	 * @return int
	 * @deprecated
	 */
	public function admin_lvl(){
		return $this->admin_lvl;
	}
	
	/**
	 * Return whether or not this road need a connection. It mean that the
	 * admin_lvl is greater than 0
	 * 
	 * @return boolean
	 */
	public function needConnection() {
		return $this->admin_lvl > 0;
	}
	
	/**
	 * Given a specific admin_lvl say whether this admin_lvl grant the access to this road or not. It means that the admin_lvl is greater to or equals the road admin_lvl
	 * 
	 * @param int $admin_lvl
	 * @return boolean
	 * @deprecated
	 */
	public function allowed($admin_lvl) {
		if (is_numeric($admin_lvl) && $admin_lvl >= $this->admin_lvl) {
			return true;
		} else {
			return false;
		}
	}
}

?>