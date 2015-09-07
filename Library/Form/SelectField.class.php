<?php

namespace Library\Form;

if (!defined("EVE_APP"))
	exit();

/**
 * Field to create a text field
 * 
 * The corresponding XML is
 * 
 * 		<form_elem form_type="SelectField">
 * 			<info name="label" value="[CONST_LABEL_NAME]" />
 * 			<info name="name" value="[field_name]" />
			<info name="textAffiche" value="[VALUE]">
 * 		</form_elem>
 * 
 * And the format of the VALUE should be
 * 		[CONST_1|val1;CONST_2;[CONST_3|val3];CONST_4|val_4]
 * 
 *  - The value should begin with a [ and end with a ]
 *  - The CONST_X are the constants of the text that are shown to the user
 *  - The valX should be a value (string, int, ...) that is shown to user
 *  - A couple of element could be replaced by another values that begin with a [ and end with a ] in a recursive way. It will provide a optgroup and the previous CONST will be the shown value of the optgroup.
 * 
 * If the number of values and the number of texts are different, an error will be thrown.
 * 
 * All the different value and the different text are separeted by a ;
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class SelectField extends Field{
	/**
	 *All the different values of the select field. the elements of the array could be
	 *
	 * - An array, then the key is the value of the optgroup and the elements of the array are inserted in the optgroup
	 * - A string, then the string is exploded on the | to have the CONST text and the value
	 * 
	 * @var mixed[]
	 */
	protected $textAffiche = array();
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\Form\Field::buildWidget()
	 */
	public function buildWidget(){
		$widget = '';
		
		if(!empty($this->errorMessage)){
			$widget .= '<div class="error">' . constant($this->errorMessage) . '</div>';
		}
		
		$widget .= '<label>' . constant($this->label) . '</label>';
		
		$widget .= '<select name="' . $this->name . '">';
		
		$widget .= $this->getListOpt($this->textAffiche);
		
		$widget .= '</select>';
		
		return $widget;
	}
	
	/**
	 * Constructor of the select and optgroup. Given the different value element, creates the optgroup
	 * 
	 * @param mixed[] $pArray
	 * @return string
	 */
	protected function getListOpt($pArray){
		$ret = '';
		
		foreach($pArray AS $key=>$elem){
			if(is_array($elem)){
				$ret .= '<optgroup label="';
				$ret .= (defined($key)) ? constant($key) : $key;
				$ret .= '">';
				$ret .= $this->getListOpt($elem);
				$ret .= '</optgroup>';
			}else{
				$val = explode('|', $elem);
				
				if (count($val) < 2)
					$val[1] = $val[0];
				
				$ret .= '<option value="' . $val[0] . '"';
				if($this->value == $val[0]){
					$ret .= ' selected';
				}
				$ret .= '>';
				$ret .= (defined($val[1])) ? constant($val[1]) : $val[1];
				$ret .= '</option>';
			}
		}
		
		return $ret;
	}
	
	/**
	 * Function that recursively create the array of the select.
	 * 
	 * - If the element begins with [, it means it is the beginning of a new group, then call recursively the function and increment the level of the group.
	 * - If the element finishes with ], it means it is the end of a group, then return the current group and the index. If the lvl is lower or equals to 0 it means that the number of braquet is too high.
	 * - Otherwise, it means it is an element, so add the current element to current array and continue.
	 * 
	 * At the end, if the level is different than zero, it means that the number of braquets was too low
	 * 
	 * @param string[] $pVal
	 * 			The array of string given by the text value
	 * @param int $key
	 * 			The current key of the array
	 * @param int $lvl
	 * 			The current level of the array (means optgroup)
	 * 
	 * @throws \IllegalArgumentException
	 * 			If there is an error on the number of braquet
	 * 
	 * @return mixed[]
	 */
	public function parseText($pVal, $key, $lvl){
		$tArray = array();
		
		for($i = $key; $i < count($pVal); $i++){
			$val = $pVal[$i];
			if(substr($val, 0, 1) == '['){
				$temp = $this->parseText($pVal, ++$i, ($lvl+1));
				$i = $temp[1];
				$tArray[substr($val, 1)] = $temp[0];
			}elseif(substr($val, -1, 1) == ']'){
				if($lvl <= 0){
					if (\Library\Application::appConfig()->getConst("LOG"))
						throw new \IllegalArgumentException("Error ID: " . \Library\Application::logger()->log("Error", "Form", "Le format des arguments n'est pas valide", __FILE__, __LINE__));
					else
						throw new \IllegalArgumentException("Le format des arguments n'est pas valide");
				}else{
					$tArray[] = substr($val, 0, -1);
					return array($tArray, $i);
				}
			}else{
				$tArray[] = $val;
			}
		}
		if($lvl != 0)
			if (\Library\Application::appConfig()->getConst("LOG"))
				throw new \IllegalArgumentException("Error ID: " . \Library\Application::logger()->log("Error", "Form", "Le format des arguments n'est pas valide", __FILE__, __LINE__));
			else
				throw new \IllegalArgumentException("Le format des arguments n'est pas valide");
		
		return array($tArray, ++$key);
	}
	
	/**
	 * Setter of the text
	 * Explode the current text by the ; and call the recursive parser
	 * 
	 * @param string $pVal
	 */
	public function setTextAffiche($pVal){
		$tArray = explode(';', $pVal);
		
		$tArray = $this->parseText($tArray, 0, 0);
		
		$this->textAffiche = $tArray[0];
	}
}

?>