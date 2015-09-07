<?php

namespace Library\Form;

if (!defined("EVE_APP"))
	exit();

/**
 * Field to create a email field
 * 
 * The corresponding XML is
 * 
 * 		<form_elem form_type="EmailField">
 * 			<info name="label" value="[CONST_LABEL_NAME]" />
 * 			<info name="name" value="[field_name]" />
 * 			<info name="value" value="[current_date]" />
 * 		</form_elem>
 * 
 * Optional element
 * 
 * 			<info name="value" value="[value]" />
 * 			<info name="maxlength" value="[max_length]" />
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class EmailField extends Field{
	/**
	 * Max length that could be inserted in the field
	 * @var String
	 */
	protected $maxlength;
	
	public function buildWidget(){
		$widget = '';
		
		if(!empty($this->errorMessage)){
			$widget .= '<div class="error">' . constant($this->errorMessage) . '</div>';
		}
		
		$widget .= '<label for="' . $this->name . '">' . constant($this->label) . '</label><input id="' . $this->name . '" type="email" name="' . $this->name . '"';
		
		if(isset($this->value) && !empty($this->value)){
			$widget .= ' value="' . $this->value . '"';
		}
		
		if(isset($this->maxlength) && !empty($this->maxlength)){
			$widget .= ' maxlength="' . $this->maxlength . '"';
		}
		
		return $widget . ' />';
	}
	
	/**
	 * Setter of the max length
	 * 
	 * @param int $pVal
	 * @throws \IllegalArgumentException
	 * 				if the max length is lower or equels to 0
	 * @return number
	 */
	public function setMaxlength($pVal){
		$maxLength = (int)$pVal;
		
		if($maxLength > 0){
			$this->maxLength = $maxLength;
			return 1;
		}else{
			if (\Library\Application::appConfig()->getConst("LOG"))
				throw new \IllegalArgumentException("Error ID: " . \Library\Application::logger()->log("Error", "Form", "La longueur maximale doit être un nombre supérieur à 0", __FILE__, __LINE__));
			else
				throw new \IllegalArgumentException("La longueur maximale doit être un nombre supérieur à 0");
			return 0;
		}
	}
}

?>