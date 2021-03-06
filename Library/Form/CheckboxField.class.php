<?php

namespace Library\Form;

if (!defined("EVE_APP"))
	exit();

/**
 * Field to create some checkbox fields
 * 
 * The corresponding XML is
 * 
 * 		<form_elem form_type="CheckboxField">
 * 			<info name="label" value="[CONST_LABEL_NAME]" />
 * 			<info name="name" value="[field_name]" />
 * 			<info name="listVal" value="[val1];[val2];..." />
 * 			<info name="textAffiche" value="[CONST_1];[CONST_2];..." />
 * 		</form_elem>
 * 
 * An optional element is
 * 
 * 			<info name="nbrCols" value="[nbrCols]" />
 * 			The default value of the nbr of column is 2
 * 
 * If the number of values and the number of texts are different, an error will be thrown.
 * 
 * All the different values and the different texts are separeted by a ;
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */

class CheckboxField extends Field{
	
	const ERROR700 = "Error 700: The number of elements to display is different from the given value or amount of names.";
	const ERROR710 = "Error 710: The number of columns must be greater than 0.";
	
	/**
	 * Number of column
	 * @var int
	 */
	protected $nbrCols = 2;
	
	/**
	 * Text to show
	 * @var string[]
	 */
	protected $textAffiche = array();
	
	/**
	 * name list
	 * @var string[]
	 */
	protected $listName = array();
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\Form\Field::buildWidget()
	 */
	public function buildWidget(){
		$widget = '';
		
		if(count($this->textAffiche) != count($this->listName)){
			throw new \InvalidArgumentException(\Library\Application::logger()->log("Error", "Form", self::ERROR700, __FILE__, __LINE__));
			return 0;
		}
		
		if(!empty($this->errorMessage)){
			$widget .= '<div class="error">' . constant($this->errorMessage) . '</div>';
		}
		
		$widget .= '<label>' . constant($this->label) . '</label>';
		
		$sWidget = array();
		
		for($i = 0; $i < count($this->textAffiche); $i++){
			$sWidget[$i] = '<input type="checkbox" name="' . $this->listName[$i] . '" ';
				
				if(isset($this->value) && !empty($this->value)){
					$sWidget[$i] .= ' checked';
				}
			$sWidget[$i] .= ' value="1" /> ' . constant($this->textAffiche[$i]);
			if(($i%$this->nbrCols) == 0 && $i != 0){
				$widget .= '<div>' . implode(' ', $sWidget) . '</div>';
				$sWidget = array();
			}
		}
		
		if(!empty($sWidget)){
			$widget .= '<div>' . implode(' ', $sWidget) . '</div>';
		}
		
		return $widget;
	}
	
	/**
	 * Setter of the text to show
	 * 
	 * @param string $pVal
	 */
	public function setTextAffiche($pVal){
		$this->textAffiche = explode(';', $pVal);
	}
	
	/**
	 * Setter of the name list
	 * 
	 * @param string $pVal
	 */
	public function setListName($pVal){
		$this->listName = explode(';', $pVal);
	}
	
	/**
	 * Setter of the column number
	 * 
	 * @param int $pVal
	 * @throws \InvalidArgumentException
	 * 			if the column number is not valid
	 * 
	 * @return number
	 */
	public function setNbrCols($pVal){
		
		$int = (int)$pVal;
		
		if($int > 0){
			$this->nbrCols = $int;
			return 1;
		}else{
			throw new \InvalidArgumentException(\Library\Application::logger()->log("Error", "Form", self::ERROR710, __FILE__, __LINE__));
			return 0;
		}
	}
}

?>