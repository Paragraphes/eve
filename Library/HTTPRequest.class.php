<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * Class that contains all the different informations that the client send to the server. It provides ways to recieve POST and GET data, the language of the user/browser, the position of the root, and the URI of the page
 * 
 * 
 * It is a subclass of {@see \Library\ApplicationComponent}
 * 
 * @see \Library\ApplicationComponent
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class HTTPRequest extends ApplicationComponent{
	
	/**
	 * A string that represent the root position of the website.
	 * @var string
	 */
	protected $root;
	
	/**
	 * A string that represents the language of the user.
	 * @var unknown
	 */
	protected $lang;
	
	/**
	 * Constructor of the {@see \Library\HTTPRequest} it provides the application and the path to the root.
	 * 
	 *  Since it is a subclass of \Library\ApplicationComponent it has to call his parent constructor with the aplication
	 * 
	 * @param Application $app
	 * @param string $root
	 */
	public function __construct(Application $app, $root){
		parent::__construct($app);
		
		if (strpos("localhost", $root) !== FALSE)
			if (substr($root, -1) != "/" && substr($root, -1) != "\\")
				$root .= '/';
		

		$this->root = $root;
	}
	
	
	/**
	 * This method provides a way to get a GET data from a specific key.
	 * 
	 * If the data is not contained in the GET data set, then it return null.
	 * @param string $key
	 * @return mixed|NULL
	 */
	public function dataGet($key){
		if($this->existGet($key)){
			return $_GET[$key];
		}else{
			return null;
		}
	}
	
	/**
	 * Gives the language of the user.
	 * 
	 * Since the language is defined in {@see self::requestUri} it has to call this method if the lang is not yet defined.
	 * 
	 * @see \Utils::getFormatLanguage()
	 * 
	 * @return string
	 */
	public function languageUser(){
		if (!isset($this->lang))
			$this->requestURI();
		
		return \Utils::getFormatLanguage($this->lang);
	}
	
	/**
	 * Returns the root value
	 * 
	 * @return string
	 */
	public function getRoot(){
		return $this->root;
	}
	
	/**
	 * Used to know whether or not a specific key has a value on the GET data set.
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function existGet($key){
		return isset($_GET[$key]);
	}
	
	/**
	 * Used to know whether or not a specific key has a value on the POST data set.
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function existPost($key){
		return isset($_POST[$key]);
	}
	
	/**
	 * Returns the language used by the navigator. It is then parsed by a {@see \Utils::getFormatLanguage()} to give a valid language that is well interpreted by the application.
	 * 
	 * @see \Utils::getFormatLanguage()
	 * 
	 * @return string
	 */
	public function languageBrowser(){
		$lang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		
		return \Utils::getFormatLanguage($lang[0]);
	}
	
	/**
	 * This method provides a way to get a POST data from a specific key.
	 * 
	 * If the data is not contained in the POST data set, then it return null.
	 * 
	 * @param string $key
	 * @return mixed|NULL
	 */
	public function dataPost($key){
		if($this->existPost($key)){
			return $_POST[$key];
		}else{
			return null;
		}
	}
	
	/**
	 * Returns the method type used to get the page, i.e. GET, HEAD, POST, PUT
	 * 
	 * @return string
	 */
	public function method(){
		return $_SERVER['REQUEST_METHOD'];
	}
	
	/**
	 * Returns the extended URI, which means all the URI with the language part if this part exists
	 * 
	 * @return string
	 */
	public function extendUri(){
		return substr($_SERVER['DOCUMENT_ROOT'] .  $_SERVER['REQUEST_URI'], strlen($this->root));
	}
	
	
	
	/**
	 * Returns the extended URI, which means all the URI without the language part
	 * 
	 * @return string
	 */
	public function requestURI(){
		
		$uri = $this->extendUri();
		
		if(preg_match("/lang-([a-z]+)/", $uri, $match)){
			
			$this->lang = $match[1];
			$uri = strstr(substr($uri, 1), '/');
		}
		
		return $uri;
	}
	
}

?>