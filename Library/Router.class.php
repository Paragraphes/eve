<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * Basic Router
 * 
 * This class contains all the different informations about the \Library\Route and a way to retrieve them from the server.
 * 
 * Since this class is abstract, it gives a way to retrieve a functional class given a DAO. It gives a kind of factory to avoid manually renaming the class we are looking for if we want another DAO.
 *
 * @see \Library\Router
 *
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 * @abstract
 */
abstract class Router {
	
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
	 * Creates all the different {@see \Library\Route} of the application. This method is used to add all the different {@see \Library\Route} to the router.
	 * 
	 * It should check from the data saved on the server to retrieve all the informations needed.
	 * 
	 * After calling this function, the argument {@see \Library\Router::$routes} has to be fulfield.
	 * 
	 * @return int
	 * @abstract
	 */
	abstract public function setRoutes();
	
	/**
	 * Retrieves all the default value of a specific {@see \Library\Route}. Given a {@see \Library\Route}, this function has to get all the different default values that have been saved on the server and field it.
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
	 * @throws \IllegalArgumentException
	 * 				If the specific DAO is not valid or it doesn't exist any {@see \Library\Router} given to this DAO
	 * 
	 * @return \Library\Router
	 */
	public static function getRouter($dao) {
		if (empty($dao) || !is_string($dao))
			if (\Library\Application::appConfig()->getConst("LOG"))
				throw new \IllegalArgumentException("Error ID: " . \Library\Application::logger()->log("Error", "Route", "DAO has to be a valid string", __FILE__, __LINE__));
			else
				throw new \IllegalArgumentException("DAO has to be a valid string");
		
		$name = "\\Library\\Router_" . strtoupper($dao);
		$router = new $name();
		
		if (!($router instanceof \Library\Router) || is_null($router))
			if (\Library\Application::appConfig()->getConst("LOG"))
				throw new \IllegalArgumentException("Error ID: " . \Library\Application::logger()->log("Error", "Route", "Try to use illegal DAO [" . $dao . "]", __FILE__, __LINE__));
			else
				throw new \IllegalArgumentException("Try to use illegal DAO [" . $dao . "]");
		
		return $router;
	}
	
	/**
	 * Returns the {@see \Library\Route} that corresponds to a url. Given an URL in parameter, it will look over all the different possible roads and returns the first one that match the url.
	 * 
	 *  It could take significan time if the list of roads is long and the specified url is at the end.
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
				throw new \Library\Exception\AccessException("Error 301", \Library\Exception\AccessException::MOVED_PERMANENTLY);
			}
		}
		throw new \Library\Exception\AccessException("Aucune route ne correspond à l'URL", \Library\Exception\AccessException::NO_ROAD);
	}
	
}

?>