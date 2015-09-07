<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * The parent of all applications.
 * 
 * Used to add components to the application. It should be able to return the application.
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 * @abstract
 */
abstract class ApplicationComponent{
	
	/**
	 * An instance of the application
	 * @var \Library\Application
	 */
	protected $app;
	
	/**
	 * The constructor ensures that all the components have an instance of {@see \Library\Application} in
	 * 
	 * @param \Library\Application $app
	 */
	public function __construct(Application $app){
		$this->app = $app;
	}
	
	/**
	 * Returns the instance of {@see \Library\Application} This instance contains references for {@see \Library\User}, 
	 * {@see \Library\HTTPRequest}, {@see \Library\HTTPResponse}, {@see \Library\Config}, {@see \Library\Language} and {@see \Library\Mailer\Mailer}
	 * 
	 * @return Application
	 */
	public function app(){
		return $this->app;
	}
	
}

?>