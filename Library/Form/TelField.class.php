<?php

namespace Library\Form;

if (!defined("EVE_APP"))
	exit();

/**
 * Field to create a telephon field
 * 
 * The corresponding XML is
 * 
 * 		<form_elem form_type="TelField">
 * 			<info name="label" value="[CONST_LABEL_NAME]" />
 * 			<info name="name" value="[field_name]" />
 * 		</form_elem>
 * 
 * Optional element
 * 
 * 			<info name="value" value="[value]" />
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class TelField extends Field{
	/**
	 * (non-PHPdoc)
	 * @see \Library\Form\Field::buildWidget()
	 */
	public function buildWidget(){
		$widget = '';
		
		if(!empty($this->errorMessage)){
			$widget .= '<div class="error">' . constant($this->errorMessage) . '</div>';
		}
		
		$widget .= '<label for="' . $this->name . '">' . constant($this->label) . '</label><input id="' . $this->name . '" type="tel" name="' . $this->name . '"';
		
		if(isset($this->value) && !empty($this->value)){
			$widget .= ' value="' . $this->value . '"';
		}
		
		return $widget . ' />';
	}
}

?>