<?php
namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * The parent of the action controller. Specific controller for some actions that need more functionality and a specific controller.
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 * @abstract
 */
abstract class ActionController extends BackController {
	
	/**
	 * Specific controller that transfers the different attributes of the controller to this controller.
	 * 
	 * @param \Library\BackController $controller
	 */
	public function __construct(\Library\BackController $controller) {
		$this->app = $controller->app();
		$this->page() = $controller->page();
		$this->managers = $controller->managers();
		$this->module = $controller->module();
		$this->action = $controller->action();
	}
	
	/**
	 * Method that executes this controller
	 * 
	 * @param \Library\HTTPRequest
	 */
	abstract public function executeAction(\Library\HTTPRequest $pRequest);
}

?>