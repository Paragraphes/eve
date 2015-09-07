<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * Factory to generate a {@see \Library\Page\Page}.
 * 
 * This class contains all the different informations needed for the creation of a {@see \Library\Page\} and field it with some data.
 * 
 * It is a subclass of {@see \Library\ApplicationComponent}
 * 
 * @see \Library\ApplicationComponent
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class PageGenerator extends ApplicationComponent{
	
	/**
	 * Support on which the {@see \Library\Page\Page} has to be displayed. By default the page will be displayed as HTML data
	 * 
	 * @var string
	 */
	protected $support = "html";
	
	/**
	 * Contains all the different attributes used to generate the page.
	 * 
	 * Those attributes need to have all the necessary information inside it, depending on the support.
	 * 
	 * @var mixed[]
	 */
	protected $attribute = array("template" => true);
	
	/**
	 * Link to the content of the page.
	 * 
	 * Not used for all the different supports
	 * 
	 * @var string
	 */
	protected $contentFile;
	
	/**
	 * List of all the different variables sent by the controler to the view. This information will be provided to the {@see \Library\Page\Page} and none of them are necessary
	 * 
	 * @var mixed[]
	 */
	protected $vars = array();
	
	/**
	 * Constructor of the class.
	 * 
	 * It has to create all the different informations that will be needed by the page such has the root and the language.
	 * 
	 * @param \Library\Application $app
	 */
	public function __construct(\Library\Application $app) {
		
		//Appel à ApplicationComponent
		parent::__construct($app);
		
		$root = 'http://'.$_SERVER['SERVER_NAME'].substr($_SERVER['REQUEST_URI'], 0, (0-strlen($this->app->httpRequest()->extendUri())));
		
		$this->addVar('root', $root);
		$this->addVar("user", $this->app()->user());
		$this->addVar('language', $this->app->httpRequest()->languageUser());
		
		$this->addVar('rootLang', (($this->app()->httpRequest()->languageUser() == null) ? $root : $root . '/lang-' . $this->app->httpRequest()->languageUser()));
	}
	
	/**
	 * GGenerator of the {@see \Library\Page\Page}.
	 * 
	 * This method will create some {@see \Library\Page\Page} depending on the support.
	 * 
	 * Then it will generate the content of the page and return it.
	 * 
	 * @see \Library\Page\Page
	 * 
	 * @throws \RuntimeException
	 * 			If the page is not valid, it means that there is not a subclass instance of {@see \Library\Page\Page}
	 */
	public function generate() {
		$page = "\\Library\\Page\\Page" . ucfirst($this->support);
		$page = new $page($this->app, $this->contentFile, $this->vars, $this->attribute);

		if (!($page instanceof \Library\Page\Page))
			if (\Library\Application::appConfig()->getConst("LOG"))
				throw new \RuntimeException("Error ID: " . \Library\Application::logger()->log("Error", "Page", "Error on Page instanciation", __FILE__, __LINE__));
			else
				throw new \RuntimeException("Error on Page instanciation");
		
		return $page->generate();
	}
	
	/**
	 * Function that returns whether the support said to load a file or not.
	 * @return boolean
	 */
	public function isToLoad() {
		return $this->support == "file";
	}
	
	/**
	 * Easy way to ask the page to load.
	 * 
	 * This method is equivalent as
	 * 		$this->setPageType("load", array("fileToLoad" => $file, "rep" => $rep))
	 * But ensure that all the necessary attribute are well defined
	 * 
	 * @param string $rep
	 * @param string $file
	 */
	public function loadData($rep, $file, $name = "") {
		$rep = __DIR__.'/..'.$rep;
		
		if (file_exists(realpath($rep.$file))) {
			$this->setPageType("file", array("fileToLoad" => $file, "rep" => $rep, "name" => $name));
		} else {
			$this->app()->httpResponse()->redirect404();
		}
	}
	
	public function showData($show = true) {
		$this->addVar("show_data", $show);
	}
	
	/**
	 * Function that returns whether the support said that it is an image or not.
	 * 
	 * @return boolean
	 */
	public function isImage() {
		return $this->support == "img";
	}
	
	/**
	 * Easy way to ask the page to use an image.
	 * 
	 * This method is equivalent as
	 * 			$this->setPageType("img", array("imageX" => $sizeX, "imageY" => $sizeY, "imageFormat" => $type))
	 * But ensure that all the necessary attribute are well defined
	 * 
	 * @param int $sizeX
	 * @param int $sizeY
	 * @param string $type
	 * @param boolean $pVal
	 */
	public function setIsImage($sizeX, $sizeY, $type, $pVal = true) {
		if ($pVal) {
			$this->setPageType("img", array("imageX" => $sizeX, "imageY" => $sizeY, "imageFormat" => $type));
		} else {
			$this->setPageType("html");
		}
	}
	
	/**
	 * Function to set the type of an image
	 * 
	 * @param string $type
	 */
	public function setImageType($type) {
		$this->attribute["imageFormat"] = $type;
	}
	
	/**
	 * Function that returns whether the support said that it is a PDF or not.
	 * 
	 * @return boolean
	 */
	public function isPdf() {
		return $this->support == "pdf";
	}
	
	/**
	 * Easy way to ask the page to use a PDF.
	 * This method is equivalent as
	 * 		$this->setPageType("pdf", array("size" => $size))
	 * But ensure that all the necessary attributes are well defined
	 * 
	 * @param boolean $pVal
	 * @param string $size
	 */
	public function setIsPdf($pVal = true, $size = "A4"){
		if ($pVal) {
			$this->setPageType("pdf", array("size" => $size));
		} else {
			$this->setPageType("html");
		}
	}
	
	/**
	 * Function that adds a font to a PDF file.
	 * 
	 * All the fonts that are on the list added by this method will be charged on the PDF
	 * 
	 * @param string $name
	 * 			Name in whiche the font will be used
	 * @param string $link
	 * 			Link to the font
	 * @param string $type
	 */
	public function setPdfFont($name, $link, $type = "") {
		if (!$key_exists("pdfFont", $this->attribute))
			$this->attribute["pdfFont"] = array();
		
		$this->attribute["pdfFont"][] = array("name" => $name, "link" => $link, "type" => $type);
	}
	
	/**
	 * Function that returns whether the support said that it is a XML or not the support.
	 * 
	 * @return boolean
	 */
	public function isXml(){
		return $this->support == "xml";
	}
	
	/**
	 * Easy way to ask the page to use a XML
	 * This method is equivalent as
	 * 		$this->setPageType("xml")
	 * But ensure that all the necessary attribute are well defined
	 * 
	 * @param boolean $pVal
	 */
	public function setIsXml($pVal = true){
		if ($pVal) {
			$this->setPageType("xml");
		} else {
			$this->setPageType("html");
		}
	}
	
	/**
	 * Function that returns whether the support said that it is a Json or not the support.
	 * 
	 * @return boolean
	 */
	public function isJson(){
		return $this->support == "json";
	}
	
	/**
	 * Easy way to ask the page to use a Json
	 * This method is equivalent as
	 * 		$this->setPageType("json")
	 * But ensure that all the necessary attribute are well defined
	 * 
	 * @param boolean $pVal
	 */
	public function setIsJson($pVal = true){
		if ($pVal) {
			$this->setPageType("json");
		} else {
			$this->setPageType("html");
		}
	}
	
	/**
	 * Method that avoids the user to use the current template.
	 * @param bool $pVal
	 */
	public function setNoTemplate($pVal = true){
		$this->attribute["template"] = !$pVal;
	}
	
	/**
	 * Adds a variable.
	 * This method adds some value linked by a key inside the data pool that will be used by the {@see \Library\Page\Page}
	 * 
	 * A key that has already be set can't be replaced until the force parameter is set to true.
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @param bool $force
	 * 
	 * @throws \RuntimeException
	 * 			If the key is not a valid string
	 * 
	 * @return number
	 */
	public function addVar($key, $value, $force = false){
		if (!is_string($key) || is_numeric($key) || empty($key))
			if (\Library\Application::appConfig()->getConst("LOG"))
				throw new \RuntimeException("Error ID: " . \Library\Application::logger()->log("Error", "Page", "The key has to be a valid String", __FILE__, __LINE__));
			else
				throw new \RuntimeException("The key has to be a valid String");
		
		if (key_exists($key, $this->vars) && !$force) {
			trigger_error("Try to remove a data in set without force");
			return 0;
		}
		
		$this->vars[$key] = $value;
		return 1;
	}
	
	/**
	 * Returns the value of a variable instead of a specific key.
	 * 
	 * If the key is linked to nothing, return null
	 * 
	 * @param string $var
	 * @return mixed|NULL
	 */
	public function getVar($var = null){
		if ($var == null)
			return $this->vars;
		
		if (isset($this->vars[$var])) {
			return $this->vars[$var];
		} else {
			return null;
		}
	}
	
	/**
	 * Sets the type of a page and specifies some attribute.
	 * 
	 * It is allowed to use directly this method but this is unsafe since no check is made to ensure that all the attributes needed for a support is provided.
	 * 
	 * @param string $support
	 * @param array $attributes
	 * 
	 * @throws \RuntimeException
	 * 			If the support is not a valid string
	 */
	public function setPageType($support, array $attributes = array()) {
		if (!is_string($support) || empty($support))
			if (\Library\Application::appConfig()->getConst("LOG"))
				throw new \RuntimeException("Error ID: " . \Library\Application::logger()->log("Error", "Page", "Le support doit être dans un format valide", __FILE__, __LINE__));
			else
				throw new \RuntimeException("Le support doit être dans un format valide");
		
		$this->support = $support;
		$this->attribute = $attributes;
	}
	
	/**
	 * Returns the current support
	 * @return string
	 */
	public function support() {
		return $this->support;
	}
	
	/**
	 * Allows to define the view file.
	 * 
	 * @param string $contentFile
	 * @throws \InvalidArgumentException => si la vue spécifiée n'est pas dans un format valide
	 * @return number
	 */
	public function setContentFile($contentFile){
		if (!is_string($contentFile) || empty($contentFile))
			if (\Library\Application::appConfig()->getConst("LOG"))
				throw new \RuntimeException("Error ID: " . \Library\Application::logger()->log("Error", "Page", "La vue spécifiée est invalide.", __FILE__, __LINE__));
			else
				throw new \RuntimeException("La vue spécifiée est invalide.");
		
		$this->contentFile = $contentFile;
		
		return 1;
	}
	
	/**
	 * Magic setter.
	 * 
	 * Allow the {@see \Library\PageGenerator} to set variable directly like other variables The method will use the {@see self::addVar} method whithout force to add the variable
	 * 
	 * @param string $var
	 * @param mixed $val
	 */
	public function __set($var, $val) {
		return $this->addVar($var, $val);
	}
	
	/**
	 * Magic getter.
	 * Allows the {@see \Library\PageGenerator} to get a variable directly like other variables
	 * 
	 * @param string $var
	 * @return Null|mixed
	 */
	public function __get($var) {
		return $this->getVar($var);
	}
	
}

?>