<?php

namespace Library\Form;

if (!defined("EVE_APP"))
	exit();

/**
 * Field to create some radio fields
 * 
 * The corresponding XML is
 * 
 * 		<form_elem form_type="RadioField">
 * 			<info name="label" value="[CONST_LABEL_NAME]" />
 * 			<info name="name" value="[field_name]" />
 * 			<info name="listVal" value="[val1];[val2];..." />
 * 			<info name="textAffiche" value="[CONST_1];[CONST_2];..." />
 * 		</form_elem>
 * 
 * An optional element is
 * 
 * 			<info name="value" value="[value]" />
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
class RadioField extends Field{
	/**
	 * The column number we want for this radio field
	 * @var int
	 */
	protected $nbrCols = 2;
	
	/**
	 * The value list the element have
	 * 
	 * @var String
	 */
	protected $listVal = array();
	
	/**
	 * The text list to show just after the radio button
	 * 
	 * @var String
	 */
	protected $textAffiche = array();
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\Form\Field::buildWidget()
	 */
	public function buildWidget(){
		$widget = '';
		
		if(count($this->textAffiche) != count($this->listVal)){
			if (\Library\Application::appConfig()->getConst("LOG"))
				throw new \IllegalArgumentException("Error ID: " . \Library\Application::logger()->log("Error", "Form", "Le nombre d'élément à afficher est différent du nombre de valeur", __FILE__, __LINE__));
			else
				throw new \IllegalArgumentException("Le nombre d'élément à afficher est différent du nombre de valeur");
			return 0;
		}
		
		if(!empty($this->errorMessage)){
			$widget .= '<div class="error">' . constant($this->errorMessage) . '</div>';
		}
		
		$widget .= '<label>' . constant($this->label) . '</label>';
		
		$sWidget = array();
		
		for($i = 0; $i < count($this->listVal); $i++){
			$sWidget[$i] = '<input type="radio" name="' . $this->name . '" value="' . $this->listVal[$i] . '"';
				if($this->listVal[$i] == $this->value){
					$sWidget[$i] .= ' checked';
				}
			$sWidget[$i] .= ' /> ' . constant($this->textAffiche[$i]);
			
			if((($i+1)%$this->nbrCols) == 0){
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
	 * Setter of the text, will explode the string by the ;
	 * 
	 * @param string $pVal
	 */
	public function setTextAffiche($pVal){
		$this->textAffiche = explode(';', $pVal);
	}
	
	/**
	 * Setter of the value, will explode the value by the ;
	 * 
	 * @param string $pVal
	 */
	public function setListVal($pVal){
		$this->listVal = explode(';', $pVal);
	}
	
	/**
	 * Setter of the number of column
	 * 
	 * @param string $pVal
	 * @throws \IllegalArgumentException
	 * 			If the number of columns is not valid, meaning it's lower or equal to 0
	 * 
	 * @return number
	 */
	public function setNbrCols($pVal){
		
		$int = (int)$pVal;
		
		if($int > 0){
			$this->nbrCols = $int;
			return 1;
		}else{
			if (\Library\Application::appConfig()->getConst("LOG"))
				throw new \IllegalArgumentException("Error ID: " . \Library\Application::logger()->log("Error", "Form", "Le nombre de colone doit être plus grand que 0", __FILE__, __LINE__));
			else
				throw new \IllegalArgumentException("Le nombre de colone doit être plus grand que 0");
			return 0;
		}
	}
}

?>