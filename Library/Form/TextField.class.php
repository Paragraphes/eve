<?php

namespace Library\Form;

if (!defined("EVE_APP"))
	exit();

/**
 * Field to create a text area in the form.
 * 
 * The corresponding XML is
 * 
 * 		<form_elem form_type="TextField">
 * 			<info name="label" value="[CONST_LABEL_NAME]" />
 * 			<info name="name" value="[field_name]" />
 * 		</form_elem>
 * 
 * Optional element
 * 
 * 			<info name="value" value="[value]" />
 * 			<info name="rows" value="[nbr_rows]" />
 * 			<info name="cols" value="[nbr_cols]" />
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class TextField extends Field{
	/**
	 * Number of columns
	 * @var int
	 */
	protected $cols;

	/**
	 * Number of rows
	 * @var int
	 */
	protected $rows;
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\Form\Field::buildWidget()
	 */
	public function buildWidget(){
		$widget = '';
		
		if(!empty($this->errorMessage)){
			$widget .= '<div class="error">' . constant($this->errorMessage) . '</div>';
		}
		
		$widget .= '<label>' . constant($this->label) . '</label><textarea name="' . $this->name . '"';
		
		if(!empty($this->rows)){
			$widget .= ' rows="' . $this->rows . '"';
		}
		
		if(!empty($this->cols)){
			$widget .= ' cols="' . $this->cols . '"';
		}
		
		$widget .= '>';
		
		if(!empty($this->value)){
			$widget .= $this->value;
		}
		
		return $widget . '</textarea>';
		
	}
	
	/**
	 * Setter of the number of columns
	 * 
	 * @param int $pVal
	 */
	public function setCols($pVal){
		$this->cols = (int)$pVal;
	}
	
	/**
	 * Setter of the number of rows
	 * 
	 * @param int $pVal
	 */
	public function setRows($pVal){
		$this->rows = (int)$pVal;
	}
}

?>