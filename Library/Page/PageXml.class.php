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
 * - The object will create a Json object and write all the public atributes plus all the value of the public method so that the name match a protected/private attribute
 * - The basic value are writen in a standard XML format
 * 
 * The maximal depth of the XML object is 20
 * 
 * @see \Library\Page\Page
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class PageXml extends Page {
	
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
			return @$DOMDocument->saveXML();
		}
		if (is_null($DOMDocument)) {
			$DOMDocument = new \DOMDocument;
			$DOMDocument->formatOutput = true;
			
			$rootNode = $DOMDocument->createElement('entries');
			$DOMDocument->appendChild($rootNode);
			
			$this->xml_encode($mixed, $rootNode, $DOMDocument, $depth);
			
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
				$class = new \ReflectionClass($mixed);
				$props = $class->getProperties(\ReflectionProperty::IS_PUBLIC);
					
				if ($class->getNamespaceName() != "Library") {
					
					$node = $DOMDocument->createElement($class->getShortName());
					$domElement->appendChild($node);
					
					foreach ($props AS $val) {
						$propName = $val->getName();
						
						$this->xml_encode(array($propName => $mixed->$propName), $node, $DOMDocument, $depth);
					}
					
					$props = $class->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE);
					$meth = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
					
					foreach ($props AS $p) {
						try {
							$geter = $p->getName();
							
							$val = $mixed->$geter();
							
							$this->xml_encode(array($p->getName() => $val), $node, $DOMDocument, $depth);
								
						} catch (\Exception $e) {
							try {
								$geter = "set" . ucfirst($p->getName());
								
								$val = $mixed->$geter();
								
								$this->xml_encode(array($p->getName() => $val), $node, $DOMDocument, $depth);
								
							} catch (\Exception $e) {
								
							}
							
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
	public function generate(){
		$this->app->httpResponse()->addHeader('Content-type: application/xml');
		
		return $this->xml_encode($this->vars); 
	}
}

?>