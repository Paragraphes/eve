<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

session_start();

/**
 * Class that contains all the informations about the user
 * 
 * This class is the standard way to use the persistant informations that are stocked in session.
 * 
 * This solution also gives some informations to know if a user is connected or not, his level of administration, his language informations and all the different stuff that is usefull for the application.
 *
 * @see \Library\ApplicationComponent
 *
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 * @abstract
 */
class User extends ApplicationComponent{
	/**
	 * Language that a user has chosen by changing his settings
	 * 
	 * @var string
	 */
	protected $language;
	
	/**
	 * Constructor of the user
	 * 
	 * since it is a subclass of \Library\ApplicationComponent, calls the parent to give it the application.
	 * 
	 * After all, if the session doesn't contain the atribute array, it creates it to avoid having errors.
	 * 
	 * @param \Library\Application $app
	 */
	public function __construct($app) {
		parent::__construct($app);
		
		if (!isset($_SESSION["attr"]) || !is_array($_SESSION["attr"]))
			$_SESSION["attr"] = array();
		
	}
	
	/**
	 * removes the attribute linked to the key given in parameter
	 * 
	 * @param string $attr
	 */
	public function unsetAttribute($attr){
		if(isset($_SESSION["attr"][$attr])){
			unset($_SESSION["attr"][$attr]);
		}
	}
	
	/**
	 * returns the value of an attribute given to a specific key
	 * 
	 * @param string $attr
	 * @return mixed|NULL
	 */
	public function getAttribute($attr){
		if(isset($_SESSION["attr"][$attr])){
			return $_SESSION["attr"][$attr];
		}else{
			return NULL;
		}
	}
	
	/**
	 * Sets the current admin lvl
	 * 
	 * The admin lvl has to be a number greater than 0
	 * 
	 * @param int $pVal
	 * @return number
	 */
	public function setAdmin($pVal){
		
		
		if (is_int($pVal) && $pVal > 0) {
			$_SESSION["admin"] = $pVal;
		} else {
			$_SESSION["admin"] = 0;
		}
		
		return 1;
	}
	
	/**
	 * Set the current admin lvl for this page
	 * 
	 * The admin lvl has to be a number greater than 0
	 * 
	 * @param int $pVal
	 * @return number
	 */
	public function setPageLvl($pVal){
		if (is_numeric($pVal) && $pVal > 0) {
			$_SESSION["page_lvl"] = $pVal;
		} else {
			$_SESSION["page_lvl"] = 0;
		}
		
		return 1;
	}
	
	
	
	/**
	 * Returns whether a user is an admin or not or not. Being an admin means that his admin lvl is greater as the admin_lvl in the global config file or greater than the application config file.
	 * 
	 * @return bool
	 */
	public function isAdmin(){
		return (($lvl = \Library\Application::appConfig()->getConst("MAX_ADMIN_LVL")) !== NULL && $lvl <= $this->getAdminLvl());
	}
	
	/**
	 * Returns the admin lvl
	 * 
	 * @return number
	 */
	public function getAdminLvl() {
		if(isset($_SESSION["page_lvl"])){
			return $_SESSION["page_lvl"];
		}
		return 0;
	}
	
	/**
	 * Returns the language used by the user. In order of priority, this language is
	 * 
	 * - The language specified in the URL
	 * - The language choosen by the user and saved on the server
	 * - The language of the browser
	 * 
	 * @return string
	 */
	public function getLanguage(){
		
		$lang = '';
		
		if($this->app->httpRequest()->languageUser() !== NULL){
			$lang = $this->app->httpRequest()->languageUser();
		}elseif(isset($this->language)){
			$lang = $this->language;
		}else{
			$lang = $this->app->httpRequest()->languageBrowser();
		}
		return \Utils::getFormatLanguage($lang);
	}
	
	/**
	 * Returns a flash information.
	 * The flash information is given only once, then is removed.
	 * 
	 * Tries to get the language traduction of the flash information if possible.
	 * 
	 * @return mixed
	 */
	public function getFlash(){
		$flash = $_SESSION['flash'];
		
		unset($_SESSION['flash']);
		
		if(defined($flash)){
			return constant($flash);
		}else{
			if(($autoFlash = $this->app()->language()->get($flash, $this->app()->httpRequest()->languageUser())) != null) {
				return $autoFlash;
			} else {
				return $flash;
			}
		}
	}
	
	/**
	 * Checks whether the user has a Flash information or not.
	 * 
	 * @return boolean
	 */
	public function hasFlash(){
		return isset($_SESSION['flash']);
	}
	
	/**
	 * Returns whether or not a user is authenticated
	 * 
	 * @return boolean
	 */
	public function isAuthenticated(){
		return (isset($_SESSION['auth']) && $_SESSION['auth'] === true);
	}
	
	/**
	 * Returns the current session id of the user
	 * 
	 * @return string
	 */
	public function getSessId(){
		return session_id();
	}
	
	/**
	 * Returns the IP adresse of the user
	 * 
	 * @return String
	 */
	public function getIP(){
		return $_SERVER['REMOTE_ADDR'];
	}
	
	/**
	 * Returns the ID of the user if connected, null otherwise
	 * 
	 * The ID could be used to retrieve the information about the user in BDD
	 * 
	 * @return int|NULL
	 */
	public function id(){
		if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])){
			return $_SESSION['user_id'];
		}else{
			return NULL;
		}
	}
	
	/**
	 * setter of the ID
	 * 
	 * @param int $pVal
	 * @throws \IllegalArgumentException
	 * 			If the ID is not valid, it means that the ID is not a number
	 * @return number
	 */
	public function setId($pVal){
		if(!is_numeric($pVal) || empty($pVal))
			if (\Library\Application::appConfig()->getConst("LOG"))
				throw new \IllegalArgumentException("Error ID: " . \Library\Application::logger()->log("Error", "User", "ID has to be numeric value", __FILE__, __LINE__));
			else
				throw new \IllegalArgumentException("ID has to be numeric value");
			
		$_SESSION['user_id'] = $pVal;
		return 1;
	}
	
	/**
	 * method to add an attribute given a key and a value
	 * 
	 * @param string $attr
	 * @param mixed $value
	 */
	public function setAttribute($attr, $value){
		$_SESSION["attr"][$attr] = $value;
	}
	
	/**
	 * Method to remove an attribute given to a key
	 * 
	 * @param string $attr
	 */
	public function removeAttr($attr) {
		if (key_exists($attr, $_SESSION["attr"]))
			unset($_SESSION["attr"][$attr]);
	}
	
	/**
	 * Function that ensures a user is authenticated
	 * 
	 * @param string $authenticated
	 * @throws \IllegalArgumentException
	 * 			If the parameter is not valid
	 */
	public function setAuthenticated($authenticated = true){
		if(!is_bool($authenticated))
			if (\Library\Application::appConfig()->getConst("LOG"))
				throw new \IllegalArgumentException("Error ID: " . \Library\Application::logger()->log("Error", "User", "Specified value for User::setAuthenticated has to be a boolean value", __FILE__, __LINE__));
			else
				throw new \IllegalArgumentException("Specified value for User::setAuthenticated has to be a boolean value");
		
		$_SESSION['auth'] = $authenticated;
	}
	
	/**
	 * Adds a flash to be shown on the next page
	 * @param string $value
	 */
	public function setFlash($value){
		$_SESSION['flash'] = $value;
	}
	
	
	/**
	 * A magic setter to add parameter like adding elements on a public property
	 * 
	 * @param string $prop
	 * @param mixed $val
	 */
	public function __set($prop, $val) {
		$this->setAttribute($prop, $val);
	}
	
	/**
	 * A magic getter to retrieve attribute like getting attributes on a public property
	 * 
	 * @param string $prop
	 * @return mixed
	 */
	public function __get($prop) {
		return $this->getAttribute($prop);
	}
	
	/**
	 * All the different informations that are used to connect an user
	 * 
	 * @param \Library\Entities\User $pUser
	 * @param mixed[] $attribute
	 * @return boolean
	 */
	public function connect(\Library\Entities\User $pUser, $attribute) {
		if ($this->isAuthenticated())
			return false;
		
		$this->setAuthenticated();
		$this->setAdmin($pUser->admin());
		$this->setId($pUser->id());
		
		$_SESSION["connected_attr"] = array_keys($attribute);
		
		foreach ($attribute AS $key => $attr)
			$this->setAttribute($key, $attr);
		
		return true;
	}
	
	/**
	 * All the different informations that are used to unconnect a user
	 * 
	 * @return boolean
	 */
	public function disconect() {
		if (!$this->isAuthenticated())
			return false;
		
		$this->setAuthenticated(false);
		$this->setAdmin(-1);
		$this->setId(-1);
		$this->setPageLvl(-1);
		
		foreach ($_SESSION["connected_attr"] AS $atr)
			$this->removeAttr($atr);
		
		$_SESSION["connected_attr"] = array();
		
		return true;
	}
}

?>