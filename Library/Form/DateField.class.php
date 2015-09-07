<?php

namespace Library\Form;

if (!defined("EVE_APP"))
	exit();

/**
 * Field to create a date field
 * 
 * The corresponding XML is
 * 
 * 		<form_elem form_type="DateField">
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
class DateField extends Field{
	/**
	 * (non-PHPdoc)
	 * @see \Library\Form\Field::buildWidget()
	 */
	public function buildWidget(){
		$widget = '';
		
		if(!empty($this->errorMessage)){
			$widget .= '<div class="error">' . constant($this->errorMessage) . '</div>';
		}
		
		$widget .= '<label for="'.$this->name.'">' . constant($this->label) . '</label>';
		
		$widget .= '<input type="text" id="' . $this->name . '" name="' . $this->name . '" class="datepicker" />';
		
		$widget .= '     <script>
			$(function() {
				$(".datepicker").datepicker({
					"regional": "fr",
					"dateFormat": "yy-mm-dd"
				});
			});
		</script>';
		
		return $widget;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\Form\Field::setValue()
	 */
	public function setValue($pVal){
		if($pVal instanceof \DateTime){
			$this->value = $pVal;
		}
	}
}

?>