<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * Class used for the form creation.
 * 
 * This class is a tool provided to create automaticaly a form using an XML file and an {@see \Library\Entity}.
 * It will automatically create the different pieces of the form respecting the standards explained in the XML
 * and fill the field with the value in the {@see \Library}.
 * It'll add a checkTime value to check the difference of time between when the form is created and when it is sent.
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class FormBuilder {
	
	const ERROR760 = "Error 760: The XML could not be parsed.";
	/**
	 * Could not find the desired XML file.
	 */
	const ERROR770 = "Error 770: The file path is invalid.";
	
	/**
	 * The representaion of the form.
	 * This value contains all the information about the form and its different field.
	 * It is here that belong the default field and the way to create the form.
	 * @var \Library\Form\Form 
	 */
	protected $form;
	
	/**
	 * Constructor of the form.
	 * We have to provide the entity for which the form is build.
	 * 
	 * @see \Library\Form\Form
	 * 
	 * @param \Library\Entity $entity
	 */
	public function __construct(\Library\Entity $entity){
		$this->setForm(new Form\Form($entity));
	}
	
	/**
	 * Function to add a field to the form. This function adds some {@see \Library\Form\Field} to the {@see \Library\Form\Form}. The different {@see \Library\Form\Field} will be added to the form in the same order than this function is called.
	 * 
	 * @param \Library\Form\Field $field
	 */
	public function addForm(\Library\Form\Field $field) {
		$this->form->add($field);
	}
	
	/**
	 * Function that build the form.
	 * Using the specified XML, the function will parse the XML to generate a new HTML form. For each of the form_elem in the xml,
	 * we try to add a new {@see \Library\Form\Field} in the {@see \Library\Form\Form} and build the HTML form for that.
	 * The type of the form will automatically be put as POST.
	 * The XML has to be formed like that
	 * 
	 * <form>
	 * 		<form_elem form_type="[FieldType]">
	 * 			<info name="label" value="[CONST_LABEL_NAME]" />
	 * 			<info name="name" value="[field_name]" />
	 * 			...
	 * 		</form_elem>
	 * 		...
	 * </form>
	 * 
	 * The explanation for the format of the different FieldType will be explained on the {@see \Library\Form\Field subClass}
	 * 
	 * @see \DOMDocument
	 * @see \Library\Form\Field
	 * 
	 * @param string $pXml
	 * 				The path to the XML file from the root
	 * @param string $pAction
	 * 				This argument is optional. It can be used if the destination of the action is not the same page.
	 * @throws \InvalidArgumentException
	 * 				Throws an exception if the path is not valid or if the xml is not well parsed.
	 * @return string
	 */
	public function build($pXml, $pAction = ''){
		if ($pAction != '') {
			$this->form->setAction($pAction);
		}
		
		$pXml = realpath($pXml);
		
		if(file_exists($pXml) && is_file($pXml)){
			$xml = new \DOMDocument();
			if ($xml->load($pXml) === false)
				throw new \InvalidArgumentException(\Library\Application::logger()->log("Error", "Form", self::ERROR760, __FILE__, __LINE__));
			
			$elements = $xml->getElementsByTagName('form_elem');
			
			foreach($elements AS $element){
				$listeInfo = array();
				$infos = $element->childNodes;
				
				foreach($infos AS $info){
					if($info instanceof \DOMElement){
						$listeInfo[$info->getAttribute('name')] = $info->getAttribute('value');
					}
				}
				
				$objName = '\\Library\\Form\\'.$element->getAttribute('form_type');
				
				$entity = $this->form->entity();
				
				$this->form->add(new $objName($listeInfo, \Library\Application::getInstance()));
			}
			return $this->form->createView();
		}else{
			throw new \InvalidArgumentException(\Library\Application::logger()->log("Error", "Form", self::ERROR770, __FILE__, __LINE__));
		}
		
	}
	
	/**
	 * Adds a {@see \Library\Form\Form} to the {@see \Library\FormBuilder} It is a simple setter that checks that it is an instance of {@see \Library\Form\Form} that is used.
	 * 
	 * @param \Library\Form\Form $pVal
	 */
	public function setForm(\Library\Form\Form $pVal){
		$this->form = $pVal;
	}
	
	/**
	 * Returns the current form
	 * 
	 * @return \Library\Form\Form
	 */
	public function form(){
		return $this->form;
	}
}

?>