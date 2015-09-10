<?php

namespace Library\Page;

if (!defined("EVE_APP"))
	exit();

/**
 * Page that returns a XML
 * 
 * No attribute is needed
 * 
 * The xml function will write all the value given in the variable array as an XML value.
 * 
 * - The ressource value will be passed
 * - The array element will return a Json array
 * - The object will create a Json object and write all the public atributes plus all the value of the public method 
 *   so that the name match a protected/private attribute
 * - The basic value are writen in a standard XML format
 * 
 * The maximal depth of the XML object is 20
 * 
 * @see \Library\Page\Page
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @author Toudoudou
 * @version 1.0
 */
class PageXml extends Page {
	
	/**
	 * The maximum depth we want to allow.
	 * @var int
	 */
	const MAX_DEPTH = 20;
	
	/**
	 * The validity bit.
	 * Set to 0 if any error occured;
	 * Set to 1 if the json was generated correctly.
	 * 
	 * @var int
	 */
	protected $valid = 1;
	
	/**
	 * Contains any errors that occured during generation.
	 * @var array
	 */
	protected $errors = array();
	
	/**
	 * The function that encodes the XML
	 * 
	 * @param mixed[] $mixed
	 * @param \DOMElement $domElement
	 * @param \DOMDocument $DOMDocument
	 * @param number $depth
	 */
	protected function xml_encode($mixed, \DOMElement $domElement = NULL, \DOMDocument $DOMDocument = NULL, $depth = 0) {
		$depth++;
		if ($depth > 20) {
			$this->valid = 0;
			$this->errors[] = "Maximum depth reached";
			return @$DOMDocument->saveXML();
		}
		if (is_null($DOMDocument)) {
			$DOMDocument = new \DOMDocument;
			$DOMDocument->formatOutput = true;
			
			$rootNode = $DOMDocument->createElement('xml');
			$DOMDocument->appendChild($rootNode);
			
			$contentNode = $DOMDocument->createElement('content');
			$rootNode->appendChild($contentNode);
			$this->xml_encode($mixed, $contentNode, $DOMDocument, $depth);
			
			$validNode = $DOMDocument->createElement('valid');
			$rootNode->appendChild($validNode);
			$validNode->appendChild($DOMDocument->createTextNode($this->valid));
			
			$errorNode = $DOMDocument->createElement('errors');
			$rootNode->appendChild($errorNode);
			$this->xml_encode($this->errors, $errorNode, $DOMDocument);
			
			return @$DOMDocument->saveXML();
		} else {
			if (is_array($mixed)) {
				foreach ($mixed as $index=>$mixedElement) {
					if (!is_resource($mixedElement)) {
						if (is_int($index) || is_resource($index)) {
							$nodeName = 'entry';
						} elseif (is_object($index)) {
							$nodeName = get_class($index);
						} else {
							$nodeName = $index;
						}
						$node = $DOMDocument->createElement($nodeName);
						$domElement->appendChild($node);
						$this->xml_encode($mixedElement, $node, $DOMDocument, $depth);
					}
				}
			} elseif (is_object($mixed)) {
				$class = new \ReflectionObject($mixed);
				$props = $class->getProperties(\ReflectionProperty::IS_PUBLIC);
					
				if ($class->getNamespaceName() != "Library") {
					
					$node = $DOMDocument->createElement($class->getShortName());
					$domElement->appendChild($node);
					
					foreach ($props AS $val) {
						$propName = $val->getName();
						
						$this->xml_encode(array($propName => $mixed->$propName), $node, $DOMDocument, $depth);
					}
					
					$props = $class->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE);
					$meth = array_map(function($m) { return $m->getName(); }, $class->getMethods(\ReflectionMethod::IS_PUBLIC));
					
					foreach ($props AS $p) {
						try {
							$pname = $p->getName();
							
							if(in_array($pname, $meth) | in_array("__call", $meth))
								$get = $pname;
							elseif(in_array("get" . ucfirst($pname), $meth))
								$get = "get" . ucfirst($pname);
							else {
								$this->valid = 0;
								$this->errors[] = "Could not get property '$pname'";
								continue;
							}
							
							$val = $mixed->$get();
							
							$this->xml_encode(array($pname => $val), $node, $DOMDocument, $depth);
								
						} catch (\Exception $e) {
							$this->valid = 0;
							$this->errors[] = "Exception while getting property '$pname'";
						}
					}
				}
				
			} else {
				$new_node = $DOMDocument->createTextNode($mixed);
	
				$domElement->appendChild($new_node);
			}
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\Page\Page::generate()
	 */
	public function generate() {
		$this->app->httpResponse()->addHeader('Content-type: application/xml');
		return $this->xml_encode($this->vars); 
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\Page\Page::generate403()
	 */
	public function generate403() {
		$this->app->httpResponse()->addHeader('Content-type: application/xml');
		$this->valid = 0;
		$this->errors[] = "403 connection lost";
		return $this->xml_encode(array());
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\Page\Page::generate404()
	 */
	public function generate404() {
		$this->app->httpResponse()->addHeader('Content-type: application/xml');
		$this->valid = 0;
		$this->errors[] = "404 connection lost";
		return $this->xml_encode(array());
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\Page\Page::generateDefaultError()
	 */
	public function generateDefaultError() {
		$this->app->httpResponse()->addHeader('Content-type: application/xml');
		$this->valid = 0;
		$this->errors[] = "connection lost";
		return $this->xml_encode(array());
	}
}

?>