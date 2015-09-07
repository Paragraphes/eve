<?php

namespace Library\Form;

if (!defined("EVE_APP"))
	exit();

/**
 * Field to create a hidden field
 * 
 * The corresponding XML is
 * 
 * 		<form_elem form_type="[FieldType]">
 * 			<info name="label" value="[CONST_LABEL_NAME]" />
 * 			<info name="name" value="[field_name]" />
 * 		</form_elem>
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class HiddenField extends Field{
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\Form\Field::buildWidget()
	 */
	public function buildWidget(){
		$widget = '';
		
		return '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '" />';
	}
}

?>