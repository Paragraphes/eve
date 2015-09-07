<?php

namespace Library\Form;

if (!defined("EVE_APP"))
	exit();

/**
 * Class used to represent an HTML form. An HTML form is defined by it's method and it's action. It is also defined by the different fields that could be answered.
 * 
 * This class automaticaly creates an hidden field that contains the checktime and both submit and reset button
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class Form {
	
	/**
	 * An instance of the {@see \Library\Entity} that is used to field the value of the different fields of the form
	 * 
	 * @var \Library\Entity
	 */
	protected $entity;
	
	/**
	 * A list of {@see \Library\Form\Field} that are in the form
	 * 
	 * @var \Library\Form\Field
	 */
	protected $fields = array();
	
	/**
	 * The action of the form
	 * 
	 * @var string
	 */
	protected $action;
	
	/**
	 * The method of the form
	 * 
	 * @var string
	 */
	protected $method;
	
	const ERROR_SAME_NAME = 1;
	
	/**
	 * Constructor of the class
	 * 
	 * @param \Library\Entity $entity
	 */
	public function __construct(\Library\Entity $entity){
		$this->setEntity($entity);
	}
	
	/**
	 * Function that adds a field for this form. When adding the field, checks if the corresponding attribute of the class has an error, adds this error to show it when generating the field.
	 * 
	 * @see \Library\Form\Field
	 * 
	 * @param Field $field
	 * 
	 * @throws \RuntimeException
	 * 			If the same name is proposed two times
	 * 
	 * @return \Library\Form\Form
	 */
	public function add(Field $field){
		$attr = $field->name();
		
		$field->setValue($this->entity->$attr());
		
		$classInfo = new \ReflectionClass($this->entity);
		$cstName = strtoupper('invalid_'.$field->name());
		
		if ($classInfo->hasConstant($cstName) && in_array($classInfo->getConstant($cstName), $this->entity->errors()))
			$field->setErrorMessage($cstName);
		
		foreach ($this->fields AS $f)
			if ($field->name() == $f->name())
				if (\Library\Application::appConfig()->getConst("LOG"))
					throw new \RuntimeException("Error ID: " . \Library\Application::logger()->log("Error", "Form", "The name " . $this->name() . " already used. No duplication allowed.", __FILE__, __LINE__), self::ERROR_SAME_NAME);
				else
					throw new \RuntimeException("The name " . $this->name() . " already used. No duplication allowed.", self::ERROR_SAME_NAME);
		
		$this->fields[] = $field;
		
		return $this;
	}
	
	/**
	 * Generates the HTML form.
	 * Generates the HTML form using all the different informations that has been provided. If some informations are missing, it'll use some default one.
	 * 
	 * Those values are
	 * 
	 * - POST for the method
	 * - the current link for the action, which means on the same file
	 * 
	 * @return string
	 */
	public function createView(){
		$view = '';
		
		$view .= '<form method="';
		
		if(isset($this->method) && !empty($this->method)){
			$view .= $this->method;
		}else{
			$view .= 'POST';
		}
		$view .= '" action="';
		
		if(isset($this->action) && !empty($this->action)){
			$view .= $this->action;
		}else{
			$view .= 'http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		}
		$view .= '">';
		$dateTime = new \DateTime('now');
		
		$view .= '<input type="hidden" name="checkTime" value="' . $dateTime->format('Y-m-d H:i:s') . '">';
		
		foreach($this->fields AS $field){
			$view .= '<p>' . $field->buildWidget() . '</p>';
		}
		
		$view .= '<p><input class="btn" type="submit" value="' . SUBMIT_FORM . '"><p>';
		$view .= '</p><input class="btn" type="reset" value="' . RESET_FORM . '"></p>';
		
		$view .= '</form>';
		
		return $view;
	}
	
	/**
	 * Sets the method to the form.
	 * 
	 * @param string $pVal
	 * @throws \InvalidArgumentException
	 * 			if the parameter is not a valid form method
	 * 
	 * @return int
	 */
	public function setMethod($pVal){
		switch($pVal){
			case 'POST':
			case 'GET':
				$this->method = $pVal;
				return $this;
				break;
			default:
				if (\Library\Application::appConfig()->getConst("LOG"))
					throw new \InvalidArgumentException("Error ID: " . \Library\Application::logger()->log("Error", "Form", "La méthode doit être de type POST ou GET", __FILE__, __LINE__));
				else
					throw new \InvalidArgumentException("La méthode doit être de type POST ou GET");
		}
	}
	
	/**
	 * Sets the action to the form
	 * 
	 * @param string $pVal
	 * @throws \InvalidArgumentException
	 * 				If the action is not valid
	 * 
	 * @return \Library\Form\Form
	 */
	public function setAction($pVal){
		if(!is_string($pVal) || empty($pVal)){
			if (\Library\Application::appConfig()->getConst("LOG"))
				throw new \InvalidArgumentException("Error ID: " . \Library\Application::logger()->log("Error", "Form", "L'action n\'est pas dans un format valide.", __FILE__, __LINE__));
			else
				throw new \InvalidArgumentException("L'action n\'est pas dans un format valide.");
		}
		$this->action = \Utils::protect($pVal);
		return $this;
	}
	
	/**
	 * Getter of the method
	 * 
	 * @return string
	 */
	public function method(){
		return $this->method;
	}
	
	/**
	 * Getter of the action
	 * 
	 * @return string
	 */
	public function action(){
		return $this->action;
	}
	
	/**
	 * Checks if a form is valid.
	 * 
	 * A form is valid if all his field are valid.
	 * 
	 * @return number
	 */
	public function isValid(){
		$valid = 1;
		
		foreach($this->fields AS $field){
			$valid *= $field->isValid();
		}
		
		return $valid;
	}
	
	/**
	 * Getter of the entity
	 * 
	 * @return \Library\Entity
	 */
	public function entity(){
		return $this->entity;
	}
	
	/**
	 * Setter of the entity
	 * 
	 * @param \Library\Entity $entity
	 * @return int
	 */
	public function setEntity(\Library\Entity $entity){
		$this->entity = $entity;
		return 1;
	}
	
}

?>