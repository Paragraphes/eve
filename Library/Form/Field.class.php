<?php

namespace Library\Form;

if (!defined("EVE_APP"))
	exit();

/**
 * Parent of all the fields.
 * 
 * Class that is the parent of all the fields of a {@see \Library\Form\Field} and provides all the different general methods used by the form.
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 * @abstract
 */
abstract class Field {
	/**
	 * The error code of the field
	 * @var int
	 */
	protected $error_code;
	
	/**
	 * The error message of the field
	 * @var string
	 */
	protected $errorMessage;
	
	/**
	 * The label of the field
	 * @var string
	 */
	protected $label;
	
	/**
	 * The name of the field
	 * @var string
	 */
	protected $name;
	
	/**
	 * The value of the field
	 * @var mixed
	 */
	protected $value;
	
	/**
	 * Constructor of the class.
	 * use the information provided in the parameter to hydrate the field
	 * 
	 * @param array $option
	 */
	public function __construct(array $option = array()){
		if(!empty($option))
			$this->hydrate($option);
	}
	
	/**
	 * Using the information, creates the HTML part of the field. If the field said there is an error, shows the error message
	 * 
	 * @return string
	 */
	abstract public function buildWidget();
	
	/**
	 * Function to set the different informations given to fill the different attributes
	 * 
	 * @param mixed[] $options
	 * @return int
	 */
	public function hydrate($options){
		$valid = 1;
		foreach($options AS $type => $value){
			$method = 'set'.ucfirst($type);
			if(is_callable(array($this, $method))){
				$fct_ok = $this->$method($value);
				$valid *= $fct_ok;
			}
		}
		return $valid;
	}
	
	/**
	 * Setter for the error message
	 * 
	 * @param string $pVal
	 * @return int
	 */
	public function setErrorMessage($pVal){
		$this->errorMessage = $pVal;
		return 1;
	}
	
	/**
	 * Getter for the error message
	 * 
	 * @return string
	 */
	public function errorMessage(){
		return $this->errorMessage;
	}
	
	/**
	 * Setter for the error code
	 * 
	 * @param int $pVal
	 * @return int
	 */
	public function setError_code($pVal){
		$this->error_code = $pVal;
		return 1;
	}
	
	/**
	 * Getter for the error code
	 * 
	 * @return int
	 */
	public function error_code(){
		return $this->error_code;
	}
	
	/**
	 * Getter of the label
	 * 
	 * @return string
	 */
	public function label(){
		return $this->label;
	}
	
	/**
	 * Getter of the name
	 * 
	 * @return string
	 */
	public function name(){
		return $this->name;
	}
	
	/**
	 * Getter of the value
	 * 
	 * @return mixed
	 */
	public function value(){
		return $this->value;
	}
	
	/**
	 * Setter for the label
	 * 
	 * @param string $pValue
	 * @return number
	 */
	public function setLabel($pValue){
		if(is_string($pValue)){
			$this->label = \Utils::protect($pValue);
			return 1;
		}
		return 0;
	}
	
	/**
	 * Setter for the name
	 * 
	 * @param string $pValue
	 */
	public function setName($pValue){
		if(is_string($pValue)){
			$this->name = \Utils::protect($pValue);
		}
	}
	
	/**
	 * Setter of the value
	 * 
	 * @param string $pValue
	 */
	public function setValue($pValue){
		$this->value = $pValue;
	}
	
	
}

?>