<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * Basic Router
 * 
 * This class contains all the different information about the \Library\Route along with a way to retrieve them from the server.
 * 
 * Since this class is abstract, it gives a way to retrieve a functional class given a DAO.
 * It gives a kind of factory to avoid manually renaming the class we are looking for if we want another DAO.
 *
 * @see \Library\Router
 *
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 * @abstract
 */
abstract class Router {
	
	const HTTP301 = "301 MOVED PERMANENTLY";
	
	const ERROR1110 = "Error 1110: DAO must be a valid string.";
	const ERROR1115 = "Error 1115: Trying to use illegal DAO [%s].";
	const ERROR1199 = "Error 1199: The ID must be a number.";
	const ERROR1120 = "Error 1120: No route found for the url [%s].";
	const ERROR1130 = "Error 1130: Variable [%s] already exists, cannot replace by force.";
	
	/**
	 * Contains a list of all the different possible {@see \Library\Route} of the application
	 * 
	 * @var \Library\Route[]
	 */
	protected $routes = array();
	
	/**
	 * Function that adds a {@see \Library\Route}
	 * 
	 * @param \Library\Route $route
	 */
	public function addRoute(Route $route){
		if(!in_array($route, $this->routes)){
			$this->routes[] = $route;
		}
	}
	
	/**
	 * Creates all the different {@see \Library\Route} of the application.
	 * This method is used to add all the different {@see \Library\Route} to the router.
	 * 
	 * It should check from the data saved on the server to retrieve all the informations needed.
	 * 
	 * After calling this function, the argument {@see \Library\Router::$routes} must be filled.
	 * 
	 * @return int
	 */
	abstract public function setRoutes();
	
	/**
	 * Retrieves all the default values of a specific {@see \Library\Route}. Given a {@see \Library\Route},
	 * this function has to get all the different default values that have been saved on the server and fill it.
	 * 
	 * @param Route $pRoute
	 * @return \Library\Route
	 */
	abstract public function addDefaultVal(Route $pRoute);
	
	/**
	 * Returns a specific {@see \Library\Router} given to a DAO. This {@see \Library\Router} has to implement all the abstract method
	 * 
	 * @param string $dao
	 * 
	 * @throws \InvalidArgumentException
	 * 				If the specific DAO is not valid or it doesn't exist any {@see \Library\Router} given to this DAO
	 * 
	 * @return \Library\Router
	 */
	public static function getRouter($dao) {
		if (empty($dao) || !is_string($dao))
			throw new \InvalidArgumentException(\Library\Application::logger()->log("Error", "Route", self::ERROR1110, __FILE__, __LINE__));
		
		$name = "\\Library\\Router_" . strtoupper($dao);
		$router = new $name();
		
		if (!($router instanceof \Library\Router) || is_null($router))
			throw new \InvalidArgumentException(\Library\Application::logger()->log("Error", "Route", sprintf(self::ERROR1115, $dao), __FILE__, __LINE__));
		
		return $router;
	}
	
	/**
	 * Returns the {@see \Library\Route} that corresponds to a url. Given an URL in parameter,
	 * it will look over all the different possible roads and returns the first one that match the url.
	 * 
	 *  It could take a little bit of time if the list of roads is long and the specified url is at the end.
	 *  
	 *
	 * @param $url
	 *
	 * @return \Library\Route
	 *
	 * @throws AccessException
	 * 			If no road matches the url
	 *
	 */
	public function getRoute($url){
		foreach($this->routes AS $route){
			
			if (($varsValues = $route->matchUrl($url)) !== false) {
				if($route->hasVars()){
					$varsNames = $route->vars();
					$listVars = array();
					
					foreach($varsValues AS $key=>$match){
						$listVars[$varsNames[$key-1]] = $match;
					}
					
					$route->setVarsListe($listVars);
				}
				
				return $this->addDefaultVal($route);
			}
		}
		
		foreach($this->routes AS $route){
			$tUrl = $route->url();
			$route->setUrl(substr($route->url(), 0, -1));
			
			if (($varsValues = $route->matchUrl($url)) !== false) {
				throw new \Library\Exception\AccessException(\Library\Application::logger()->log("Error", "Route", self::HTTP301, __FILE__, __LINE__), \Library\Exception\AccessException::MOVED_PERMANENTLY);
			}
		}
		throw new \Library\Exception\AccessException(\Library\Application::logger()->log("Error", "Route", sprintf(self::ERROR1120, $url), __FILE__, __LINE__), \Library\Exception\AccessException::NO_ROAD);
	}
	
}

?>