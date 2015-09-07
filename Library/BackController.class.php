<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * The parent of all controllers.
 * The controller are components of application. So they extend {@see \Library\ApplicationComponent}.
 * The controller are the part of the application that use the data given by the model to give them to the view.
 * 
 * @see \Library\ApplicationComponent
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @author Toudoudou
 * @version 1.0
 * @abstract
 */
abstract class BackController extends ApplicationComponent{
	
	/**
	 * The controller is attempting to execute an action that doesn't exist in the current module.
	 */
	const ERROR200 = "Error 200: The action [%s] isn't defined in this module.";
	/**
	 * Tried to set the controller's action to an empty string or non-string argument.
	 */
	const ERROR210 = "Error 210: The action must be a valid string.";
	/**
	 * Tried to set the controller's view to an empty string or non-string argument.
	 */
	const ERROR220 = "Error 220: The view must be a valid string.";
	/**
	 * Tried to set the controller's module to an empty string or non-string argument.
	 */
	const ERROR230 = "Error 230: The module must be a valid string.";
	
	/**
	 * The name of the action that we need to be performed.
	 * This action will be the name of different elements
	 * 
	 * - the name of the method used will be : execute[Action]
	 * - the name of the view (if used)
	 * - the name of the language file of the action (if used)
	 * 
	 * @var string
	 */
	protected $action = '';
	
	/**
	 * The name of the module used by the controller.
	 * The name of the module is used for
	 * 
	 * - The name of the controller
	 * - The location of the controller
	 * - The name of the global language file of the module
	 * 
	 * @var string
	 */
	protected $module = '';
	
	/**
	 * The page that has to be returned to the view. This page is used to add and retrieve data We could also give the type of page user.
	 * 
	 * By default, an HTML page is returned to the client.
	 * 
	 * @var \Library\PageGenerator
	 */
	protected $page = NULL;
	
	/**
	 * This is an instance of the manager used in order to use the saved data.
	 * This manager has to give some specific functions to use the data.
	 * @var \Library\Managers
	 */
	protected $managers = NULL;
	
	/**
	 * Controller constructor. It has to create all the differents variables used in this class
	 * 
	 * - It has to give the application back to his parent
	 * - Creates a new {@see \Library\PageGenerator}
	 * - Creates a new {@see \Library\Manager} from the information in the {@see \Library\AppConfig}
	 * - sets the modlue {@see self::setModule()}
	 * - sets the language file {@see self::setLanguage()}
	 * - sets the action {@see self::setAction()}
	 * - sets the view {@see self::setView()}
	 * 
	 * @see \Library\PageGenerator
	 * @see \Library\Manager
	 * 
	 * @param Application $app
	 * @param string $module
	 * @param string $action
	 */
	public function __construct(Application $app, $module, $action){
		
		//Appel à ApplicationComponent
		parent::__construct($app);
		
		//Crée la page de l'application
		$this->page = new PageGenerator($app);
		
		//Crée un Managere qui devra utiliser PDO
		$this->managers = new Managers(\Library\Application::appConfig()->getConst("DAO"), DAO_Factory::getConnexion(\Library\Application::appConfig()->getConst("DAO")), $module);
		
		$this->setModule($module);
		
		$this->setLanguage($module, $action);
		$this->setAction($action);
		$this->setView($action, $module);
		
	}
	
	/**
	* Used to execute the action.
	* There is two different possibilities for the execution of an action.
	* First it could have his specific controller, so it checks if this controller exists or not.
	* If a controller exists in /Modules/ModuleName/Controller/ActionController.class.php then
	* this class is instanciated and the controller is loaded. This class has to extend {@see \Library\ActionController}
	* 
	* If the specific controller doesn't exist, then it should look if the current controller
	* contains a method that correspond to this specific action
	* 
	* - If yes, then use it and send it the {@see \Library\HTTPRequest} from the {@see \Library\Application}
	* - If no, throw an exception
	*
	* @throws RuntimeException
	* 			If that action doesn't exist in controller
	*/
	public function execute() {
		if (file_exists(__DIR__ . "/../Modules/" . $this->module . "/Controller/" . ucfirst($this->action) . "Controller.class.php")) {
			//require(__DIR__ . "/../Modules/" . $this->module . "/Controller/" . ucfirst($this->action) . "Controller.class.php");
			$className = ucfirst($this->action) . "Controller";
			$class = new $className($this);
			
			if ($class instanceof \Library\ActionController)
				return $class->executeAction($this->app->httpRequest());
		}
		
		$methode = 'execute' . ucfirst($this->action);
		
		if(!is_callable(array($this, $methode))){
			throw new \RuntimeException(\Library\Application::logger()->log("Error", "Controller-InvalidAction", sprintf(self::ERROR200, $this->action), __FILE__, __LINE__));
		}
		
		$this->$methode($this->app->httpRequest());
		
	}
	
	/**
	 * Gives back the current {@see \Library\PageGenerator} The given page contains all the variable that will be needed by the view.
	 * The PageGenerator will create its own {@see \Library\Page} for the client.
	 * 
	 * @return \Library\PageGenerator
	 */
	public function page(){
		return $this->page;
	}
	
	/**
	 * Loads the Language file.
	 * The method checks whether ot not a file exist in the user language. If not, it checks whether or not a file exists in default language. If not, it dosen't load anything.
	 * 
	 * These action are performed for
	 * 
	 * - The global module language file
	 * - The specific action language file
	 * 
	 * None of them are necessary.
	 * 
	 * @param string $module
	 * @param string $action
	 */
	public function setLanguage($module, $action){
		
		if(file_exists(__DIR__ . '/../Modules/' . $module . '/ModuleLang/module.' . $this->app->user()->getLanguage() . '.php')){
			require(__DIR__ . '/../Modules/' . $module . '/ModuleLang/module.' . $this->app->user()->getLanguage() . '.php');
		}else if (file_exists(__DIR__ . '/../Modules/' . $module . '/ModuleLang/module.' . \Utils::defaultLanguage() . '.php')) {
			require(__DIR__ . '/../Modules/' . $module . '/ModuleLang/module.' . \Utils::defaultLanguage() . '.php');
		}
		
		if(is_file(__DIR__ . '/../Modules/' . $module . '/Lang/' . $action . '.' . $this->app->user()->getLanguage() . '.php')){
			require(__DIR__ . '/../Modules/' . $module . '/Lang/' . $action . '.' . $this->app->user()->getLanguage() . '.php');
		}else if (file_exists(__DIR__ . '/../Modules/' . $module . '/Lang/' . $action . '.' . \Utils::defaultLanguage() . '.php')) {
			require(__DIR__ . '/../Modules/' . $module . '/Lang/' . $action . '.' . \Utils::defaultLanguage() . '.php');
		}
		
	}
	
	/**
	 * Sets the view of the controler.
	 * 
	 * This function checks whether or not the view is valid (it means that the view has to be a non empty string)
	 * 
	 * Then it gives the view to the {@see \Library\PageGenerator::setContentFile()}
	 * 
	 * @param string $view
	 * @throws \InvalidArgumentException
	 * 				If the view is not valid
	 */
	public function setView($view, $module){
		if(!is_string($view) || empty($view))
			throw new \InvalidArgumentException(\Library\Application::logger()->log("Error", "InvalidView", self::ERROR220, __FILE__, __LINE__));
		
		$this->page->setContentFile(__DIR__ . '/../Modules/' . $module . '/Views/' . $view . '');
		
	}
	
	/**
	 * Sets the module of the controler.
	 * 
	 * This function checks whether the module is valid or not (it means that the module has to be a non empty string)
	 * 
	 * Then it saves the module
	 * 
	 * @param string $module
	 * @throws \InvalidArgumentException
	 * 				If the module is not valid
	 */
	public function setModule($module){
		if(!is_string($module) || empty($module)){
			throw new \InvalidArgumentException(\Library\Application::logger()->log("Error", "InvalidModule", self::ERROR230, __FILE__, __LINE__));
		}
		
		$this->module = $module;
	}
	
	/**
	 * Sets the action of the controler.
	 * 
	 * This function checks whether or not the action is valid (it means that the action has to be a non empty string)
	 * 
	 * Then it saves the action
	 * 
	 * @param string $action
	 * @throws \InvalidArgumentException
	 * 				If the action is not valid
	 */
	public function setAction($action){
		if(!is_string($action) || empty($action)){
			throw new \InvalidArgumentException(\Library\Application::logger()->log("Error", "InvalidAction", self::ERROR210, __FILE__, __LINE__));
		}
		
		$this->action = $action;
	}
	
	/**
	 * Returns the current {@see \Library\Manager}
	 * This manager is used to interact with the data that are saved on the server.
	 * 
	 * @return \Library\Managers
	 */
	public function managers() {
		return $this->managers;
	}
	
	/**
	 * Getter of the module
	 * @return string
	 */
	public function module () {
		if (isset($this->module))
			return $this->module;
		else
			return "";
	}
	
	/**
	 * Getter of the action
	 * @return string
	 */
	public function action () {
		if (isset($this->action))
			return $this->action;
		else
			return "";
	}
	
	public function utilRecursiveFunction(array $baseArray, \Library\Manager $manager, $way = 0) {
		foreach ($baseArray AS $key => $elem) {
			$baseArray[$key] = array($elem);
			switch ($way) {
				case 1:
					$baseArray[$key][] = $this->utilRecursiveFunction($manager->getList(array("parent_id = :pId"), array(array("key" => ":pId", "val" => $elem->id(), "type" => \PDO::PARAM_INT))), $manager, $way);
					break;
				case 0:
				default:
					if ($elem->parent_id() != 0)
						$baseArray[$key][] = $this->utilRecursiveFunction(array($manager->get($elem->parent_id())), $manager, $way);
			}
		}
		return $baseArray;
	}
	
	public function recursiveElement(array $baseArray, \Library\Manager $manager, $way = 0) {
		$stdClass = (object) array('aFlat' => array());
		
		@array_walk_recursive($this->utilRecursiveFunction($baseArray, $manager, $way), create_function('&$v, $k, &$t', '$t->aFlat[] = $v;'), $stdClass);
		
		$fArray = array();
		
		foreach ($stdClass AS $array)
			foreach ($array AS $elem)
				if (!key_exists($elem->id(), $fArray))
					$fArray[$elem->id()] = $elem;
		
		return $fArray;
	}
	
}

?>