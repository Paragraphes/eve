<?php

namespace Library\Form;

if (!defined("EVE_APP"))
	exit();

/**
 * Class used to represent an HTML form. An HTML form is defined by its method and its action.
 * It is also defined by the different fields that could be answered.
 * 
 * This class automaticaly creates an hidden field that contains the checktime and both submit and reset button
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @author Toudoudou
 * @version 1.0
 */
class Form {
	const ERROR720 = "Error 720: The name [%s] is already in use. No duplicates allowed.";
	const ERROR730 = "Error 730: The method must be of type POST or GET.";
	/**
	 * Tried to set the form's action to an empty string or non-string argument.
	 */
	const ERROR740 = "Error 740: The action is not in a valid format.";
	
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
	 * Function that adds a field for this form. When adding the field, 
	 * checks if the corresponding attribute of the class has an error,
	 * adds this error to show it when generating the field.
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
				throw new \RuntimeException(\Library\Application::logger()->log("Error", "Form", sprintf(self::ERROR720, $this->name()), __FILE__, __LINE__), self::ERROR_SAME_NAME);
		
		$this->fields[] = $field;
		
		return $this;
	}
	
	/**
	 * Generates the HTML form.
	 * Generates the HTML form using all the different informations that has been provided.
	 * If some informations are missing, it'll use some default one.
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
				throw new \InvalidArgumentException(\Library\Application::logger()->log("Error", "Form", self::ERROR730, __FILE__, __LINE__));
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
			throw new \InvalidArgumentException(\Library\Application::logger()->log("Error", "Form", self::ERROR740, __FILE__, __LINE__));
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