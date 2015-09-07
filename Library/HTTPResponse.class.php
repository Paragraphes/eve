<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * Class that contains the different informations that will be returned to the client browsers.
 * 
 * It's in this class that we generate the class, using the {@see \Library\PageGenerator} and all the data that are stored in
 * 
 * It is a subclass of {@see \Library\ApplicationComponent}
 * 
 * @see \Library\ApplicationComponent
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class HTTPResponse extends ApplicationComponent{
	
	/**
	 * An instance of {@see \Library\PageGenerator} used to show the page.
	 * 
	 * @var \Library\PageGenerator
	 */
	protected $page;
	
	/**
	 * Method that adds a header to the browser
	 * @param String $header
	 */
	public function addHeader($header){
		header($header);
	}
	
	/**
	 * Used to redirect the page to another location
	 * 
	 * @param string $location
	 * 		the location of the redirection
	 */
	public function redirect($location){
		header('Location: ' . $location);
		exit();
	}
	
	/**
	 * Function that redirects the user on a custom 404 page.
	 * 
	 * It should be used to tell there is no page at the specific URL. It should also be used to hide a protected page.
	 */
	public function redirect404($support = ""){
		if ($support == "")
			$support = $this->page->support();
		
		$this->page = new PageGenerator($this->app);
		$this->page->setContentFile(__DIR__ . '/../Errors/404');
		$this->page->setPageType($support);
		$this->page->setError("404");
		
		$this->addHeader('HTTP/1.0 404 Not Found');
		
		$this->send();
	}
	
	/**
	 * Function that redirects the user on a custom 301 page.
	 * 
	 * It should be used to tell there is no page at the specific URL. It should also be used to hide a protected page.
	 */
	public function redirect301($url = ""){
		if ($url == "")
			$url = $this->app()->httpRequest()->getRoot() . $this->app()->httpRequest()->extendUri() . '/';
		
		$this->addHeader('HTTP/1.1 301 Moved Permanently');
		$this->addHeader("Location: " . $url);
	}
	
	/**
	 * Function that redirects the user on a custom 403 page.
	 * 
	 * It should be used to tell the user he is not allowed to go on a specific URL.
	 */
	public function redirect403($support = ""){
		if ($support == "")
			$support = $this->page->support();
		
		$this->page = new PageGenerator($this->app);
		$this->page->setContentFile(__DIR__ . '/../Errors/403');
		$this->page->setPageType($support);
		$this->page->setError("403");
		
		$this->addHeader('HTTP/1.0 403 Forbidden');
		
		$this->send();
	}
	
	/**
	 * Stops the script and generate the web page.
	 * 
	 * This function has to stop the script, generate the page with all it specification and send it to the browser.
	 */
	public function send(){
		exit($this->page->generate());
	}
	
	/**
	 * Seter for the {@see \Library\PageGenerator}. It is used to change the {@see \Library\PageGenerator} of the application.
	 * 
	 * @param \Library\PageGenerator $page
	 */
	public function setPage(PageGenerator $page){
		$this->page = $page;
	}
	
}

?>