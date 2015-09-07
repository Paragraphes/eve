<?php
namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * The parent of all applications.
 * 
 * To make the application work, we have to call it with the first file, and this class will charge everything it needs.
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 * @abstract
 */
abstract class Application{
	
	/**
	* Object client request that contains all the data from the client
	* 
	* Instanciate in {@see self::__construct()}
	* 
	* @var \Library\HTTPRequest
	*/
	protected $httpRequest;
	
	/**
	* Object client response that contains all the data for the client
	* 
	* Instanciate in {@see self::__construct()}
	* @var \Library\HTTPResponse
	*/
	protected $httpResponse;
	
	/**
	* Name of the application
	* Instanciate in {@see subclass::__construct()}
	* @var string
	*/
	protected $name;
	
	/**
	 * Object that gets configuration information from the server
	 * Instanciate in {@see self::getCosetConfigureClassntroler()}
	 * @var \Library\Config
	 */
	protected $config;
	
	/**
	 * Object that gives the base informations for an application that is not in the server
	 * Instanciate in {@see self::getControler()}
	 * @var \Library\AppConfig
	 */
	protected static $appConfig;
	
	/**
	 * Object that gets language information from the server
	 * Instanciate in {@see self::setConfigureClass()}
	 * @var \Library\Language
	 */
	protected $language;
	
	/**
	* Object that gives information about the user.
	* Instanciate in {@see self::__construct}
	* @var \Library\User
	*/
	protected $user;
	
	/**
	 * Object that can send email
	 * Instanciate in {@see self::mailer()}
	 * @var \Library\Mailer\Mailer
	 */
	protected $mailer;
	
	/**
	 * Object that logs information
	 * Instanciate in {@see self::getControler()}
	 * @var \Library\Logger
	 */
	protected static $logger;
	
	/**
	 * Constructor of the application. The subclass needs to call it in order to give a name to the app.
	 * 
	 * @param string $root
	 * 				The root of the application. Used to know the position in the file tree.
	 */
	public function __construct($root) {
		
		if (!isset($this->name))
			throw new \RuntimeException("The application should have a name");
		
		$configRoot = "\\Applications\\" . $this->name . "\\Config\\Config";
		self::$appConfig = new $configRoot();
		
		self::$logger = new \Library\Logger($this, './Log');
		
		$this->httpRequest = new HTTPRequest($this, $root);
		$this->httpResponse = new HTTPResponse($this);
		
		$this->dataTyper = new DataTyper(self::$appConfig->getConst("DAO"));
		
		$this->user = new User($this);
		
		\Library\Entity::pushApplication($this);
	}
	
	/**
	 * Object that gives the current controler.
	 * This method will perform different actions
	 * 
	 * - Create a log class
	 * - Get the application configuration
	 * - Get the application language constant
	 * - Set the current link with the right language
	 * - If the user want to disconnect, let him leave
	 * - Get the different road from the DB
	 * - Check if a road match with the current URI {@see \Library\Router::getRoute()}
	 * - If a road matches, the user is not connected and the road needs a connection, use the connexionController
	 * - Get all the different variables from the URI and add them to $_GET
	 * - Generate the current controller
	 * - If the controller is not well formated, it means that the controller is not a subclass of BackController and we're not able to ensure that the Controller will work.
	 * - Create {@see \Library\Config} and {@see \Library\Language}
	 * - Return a working controler
	 * 
	 *
	 * @see \Library\Logger
	 * @see \Library\HTTPRequest
	 * @see \Library\HTTPResponse
	 * @see \Library\Router
	 * @see \Library\Route
	 * @see \Library\BackController
	 * @see \Modules\Connexion\ConnexionController
	 * @see \Library\GrantAccess
	 * @see \Library\Exception\AccessException
	 * 						the controler of the application
	 *
	 * @return \Library\BackController the controler of the application
	 */
	public function getController(){
		if(is_file(__DIR__ . '/../Applications/' . $this->name() . '/Lang/base.' . $this->user()->getLanguage() . '.php')){
			require(__DIR__ . '/../Applications/' . $this->name() . '/Lang/base.' . $this->user()->getLanguage() . '.php');
		}else{
			require(__DIR__ . '/../Applications/' . $this->name() . '/Lang/base.' . \Utils::defaultLanguage() . '.php');
		}
		
		/**
		 * Set the language for the application
		 */
		$lang = ($this->httpRequest()->languageUser() != NULL) ? 'http://'.$_SERVER['SERVER_NAME'].substr($_SERVER['REQUEST_URI'], 0, (0-strlen($this->httpRequest()->extendUri()))).'/lang-'.$this->httpRequest()->languageUser() : 'http://'.$_SERVER['SERVER_NAME'].substr($_SERVER['REQUEST_URI'], 0, (0-strlen($this->httpRequest()->extendUri())));
		$lang .= '/';
		
		/**
		 * Define all the different constant used in the app
		 */
		try{
		
			if ($this->httpRequest->existGet("deco")) {
				throw new \Library\Exception\AccessException("Unconnect", \Library\Exception\AccessException::DECONEXION);
			}
			
			$router = \Library\Router::getRouter(self::$appConfig->getConst("DAO"));
			
			$router->setRoutes();
			
			/**
			 * On regarde si une des routes match avec la demande client. Si une erreur RuntimeException est levée, on affiche 404 not found
			 * Si une route match, on récupère les variables de la route avec leurs valeurs.
			 */
			$matchedRoute = $router->getRoute($this->httpRequest->requestURI());
			
			/**
			 * Si la route demande une connection, alors on regarde que l'utilisateur est autentifié et a les droits d'accès
			 * Si l'utilisateur n'est pas autentifié, on lui demande de se connecter
			 * Si il est autentifié mais n'a pas les droits d'accès, on le redirige sur 403 not allowed
			 */
			
			if ($matchedRoute->needConnection() && !$this->user->isAuthenticated()) {
				$class = "Modules\\Connexion\\ConnexionController";
				$controller = new $class($this, 'Connexion', 'index');
			} else {
				$vars = $matchedRoute->varsListe();
				
				$infoAccess = \Library\GrantAccess::grantAccess("PDO", $matchedRoute, $this->user->id());
				
				$this->user()->setPageLvl($infoAccess);
				
				//On ajoute les variables à $_GET
				$_GET = array_merge($_GET, $vars);
				
				//On crée le controler du module associé à la route dans l'application. Il s'agit du module défini par l'attribut module du XML
				// et le controller se nomme [nom du model]Controller
				$controllerClass = 'Modules\\' . $matchedRoute->module() . '\\' . $matchedRoute->module() . 'Controller';
				
				$controller = new $controllerClass($this, $matchedRoute->module(), $matchedRoute->action());
			}
			
		}catch(\Library\Exception\AccessException $e){
			switch ($e->getCode()) {
				case \Library\Exception\AccessException::DECONEXION :
					if (\Library\Application::appConfig()->getConst("LOG")) {
						\Library\Application::logger()->log("Connection", "Unconnect", "Unconnection", __FILE__, __LINE__, false);
					}
					$_SESSION['flash'] = 'UNCONNECT_CONNECTION';
					break;
				case \Library\Exception\AccessException::TIME_FINISHED :
					if (\Library\Application::appConfig()->getConst("LOG")) {
						\Library\Application::logger()->log("Connection", "time_finished", "Too much time between action", __FILE__, __LINE__, false);
					}
					$_SESSION['flash'] = 'TIME_CONNECTION';
					break;
				case \Library\Exception\AccessException::NO_ROAD :
					if (\Library\Application::appConfig()->getConst("LOG")) {
							if (!preg_match('#^/Web/#',$this->httpRequest->requestURI()))
								\Library\Application::logger()->log("Error", "NoRoad", "Try to access not existing road [" . $this->httpRequest->requestURI() . "]", __FILE__, __LINE__);
							else
								\Library\Application::logger()->log("Error", "MissRess", "Try to get missing ressource [" . $this->httpRequest->requestURI() . "]", __FILE__, __LINE__);
					}
					$this->httpResponse->redirect404();
				case \Library\Exception\AccessException::NOT_ALLOWED :
					if (\Library\Application::appConfig()->getConst("LOG")) {
						\Library\Application::logger()->log("Connection", "not_allowed", "Try to access on not allowed road [" . $this->httpRequest()->requestURL() . "]", __FILE__, __LINE__, false);
					}
					$this->httpResponse()->redirect403();
					break;
				case \Library\Exception\AccessException::MOVED_PERMANENTLY:
					$this->httpResponse->redirect301();
					break;
				default:
					$this->httpResponse->redirect403();
			}
			session_destroy();
			$_SESSION = array();
			$this->httpResponse()->redirect($lang);
		}
		
		if (! $controller instanceof BackController) {
				if (\Library\Application::appConfig()->getConst("LOG")) {
					ob_start();
					var_dump($controller);
					$ret = ob_get_clean();
					throw new \RuntimeException("Error ID: " . \Library\Application::logger()->log("Error", "InvalidControler", "Controler not valid [" . $ret . "]", __FILE__, __LINE__), \Library\Exception\AccessException::DECONEXION);
				} else
					throw new \RuntimeException("Erreur, le contrôleur n'est pas valide");
			
		}
		
		$this->setConfigClass($controller->managers());
		
		//On retourne une instance du controler.
		return $controller;
	}
	
	/**
	 * Method that should run the current application
	 * 
	 * @abstract
	 * @see subClass
	 */
	abstract public function run();
	
	/**
	 * Function that creates an instance of {@see \Library\Config} and {@see \Library\Language} and gives them a working manager
	 * 
	 * @see \Library\Config
	 * @see \Library\Language
	 * @see \Library\Managers
	 * 
	 * @param Managers $pManager
	 * 		The current manager
	 */
	public function setConfigClass(Managers $pManager) {
		$this->config = new Config($pManager->getManagersOf("config", 0));
		
		$this->language = new Language($pManager->getManagersOf("language", 0));
	}
	
	// Getter
	/**
	 * Returns the used instance of {@see \Library\HTTPRequest}
	 * It can give the information from the client
	 * 
	 * @return \Library\HTTPRequest
	 */
	public function httpRequest(){
		return $this->httpRequest;
	}
	
	/**
	 * Returns the used instance of {@see \Library\Config}
	 * It can give the configuration information of different informations from a manager
	 * @return \Library\Config
	 */
	public function config(){
		return $this->config;
	}
	
	/**
	 * Returns the used instance of {@see \Library\Language}
	 * It can give the translation of information in different languages from a manager
	 * @return \Library\Language
	 */
	public function language(){
		return $this->language;
	}
	
	/**
	 * Return the used instance of {@see \Library\User}
	 * It contains all the information about the user
	 * @return \Library\User
	 */
	public function user(){
		return $this->user;
	}
	
	/**
	 * Returns the used instance of {@see \Library\HTTPResponse}
	 * It is used to give back information to the client from the server
	 * @return \Library\HTTPResponse
	 */
	public function httpResponse(){
		return $this->httpResponse;
	}
	
	/**
	 * Returns the current name of the application.
	 * This name should be the name of the folder of the application.
	 * @return String
	 */
	public function name(){
		return $this->name;
	}
	
	/**
	 * Returns an instance of the {@see \Library\Mailer\Mailer}.
	 * If the mailer doesn't exist, creates a new one, since it is not used in each page.
	 * @return \Library\Mailer
	 */
	public function mailer(){
		if (!isset($this->mailer))
			$this->mailer = new \Library\Mailer\Mailer($this);
		
		return $this->mailer;
	}
	
	/**
	 * Returns an instance of {@see \Library\AppConfig}
	 * It contains all the basic information from the application whithout a communication with the manager.
	 * @static
	 * @return \Library\AppConfig
	 */
	public static function appConfig() {
		return self::$appConfig;
	}
	
	
	/**
	 * Returns an instance of {@see \Libray\Logger}
	 * It contains all the method to log information.
	 * @return \Library\Logger
	 */
	public static function logger() {
		return self::$logger;
	}
}

?>