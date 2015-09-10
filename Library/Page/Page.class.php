<?php

namespace Library\Page;

if (!defined("EVE_APP"))
	exit();

/**
 * Base class of all different pages.
 * 
 * This class provides the base attribute for the different pages and define the function that a class has to have.
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @author Toudoudou
 * @version 1.0
 * @abstract
 */
abstract class Page extends \Library\ApplicationComponent {
	
	/**
	 * Link to the content file page
	 * @var string
	 */
	protected $contentFile;
	
	/**
	 * Array of page variables
	 * @var mixed[]
	 */
	protected $vars = array();
	
	/**
	 * Array of page attributes
	 * @var mixed[]
	 */
	protected $attribute = array();
	
	/**
	 * Constructor of the page, has all the basic variables
	 * 
	 * @param \Library\Application $app
	 */
	public function __construct(\library\Application $app, $contentFile, $vars, $attribute) {
		parent::__construct($app);
		
		$this->contentFile = $contentFile;
		$this->vars = $vars;
		$this->attribute = $attribute;
	}
	
	/**
	 * This function has to generate the page
	 * 
	 * The function checks that all the different needed attributes are provided, then generates the page for the user.
	 * 
	 * It has to change the header in order to match the returning page type
	 * 
	 * @throws \InvalidArgumentException
	 * 			if the needed attributes are not provided
	 * 
	 * @return string
	 */
	abstract public function generate();
	
	/**
	 * This function is used to decide what to display in the event of a 403 error.
	 * 
	 * @return string
	 */
	public function generate403() {
		$content = "403: Access Forbidden";
		
		ob_start();
		if (key_exists("template", $this->attribute) && $this->attribute["template"])
			require(__DIR__ . '/../../Applications/' . $this->app->name() . '/Templates/layout.php');
		else
			echo $content;
		
		return ob_get_clean();
	}
	
	/**
	 * This function is used to decide what to display in the event of a 404 error.
	 * 
	 * @return string
	 */
	public function generate404() {
		$content = "404: Page Not Found";
		
		ob_start();
		if (key_exists("template", $this->attribute) && $this->attribute["template"])
			require(__DIR__ . '/../../Applications/' . $this->app->name() . '/Templates/layout.php');
		else
			echo $content;
		
		return ob_get_clean();
	}
	
	/**
	 * This function is used to display an error when no matching code was found.
	 * 
	 * @return string
	 */
	public function generateDefaultError() {
		$content = "Error";
		
		ob_start();
		if (key_exists("template", $this->attribute) && $this->attribute["template"])
			require(__DIR__ . '/../../Applications/' . $this->app->name() . '/Templates/layout.php');
		else
			echo $content;
		
		return ob_get_clean();
	}
}

?>