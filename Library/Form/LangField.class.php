<?php

namespace Library\Form;

if (!defined("EVE_APP"))
	exit();

/**
 * Field to create a special select field for the different languages
 * 
 * The corresponding XML is
 * 
 * 		<form_elem form_type="LangField">
 * 		</form_elem>
 * 
 * This field automatically creates the different values
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class LangField extends SelectField{
	/**
	 * List of value
	 * @var string[]
	 */
	protected $listVal = array();
	
	/**
	 * Text list to show
	 * @var string[]
	 */
	protected $textAffiche = array();
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\Form\SelectField::buildWidget()
	 */
	public function buildWidget($currLangue = null){
		$listLang = \Utils::getListLanguage();
		
		$this->listVal = array_keys($listLang);
		$this->textAffiche = array_values($listLang);
		$this->value = \Utils::getFormatLanguage($currLangue);
		
		return parent::buildWidget();
	}
}

?>