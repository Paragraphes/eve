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
}

?>