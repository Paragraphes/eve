<?php

namespace Library\Page;

if (!defined("EVE_APP"))
	exit();

/**
 * PPage that returns a Json.
 * 
 * No attribute is needed
 * 
 * The json function will write all the values given in the variable array as a Json value.
 * 
 * - The null value will return null
 * - The ressource value will return "ressource"
 * - The array element will return a Json array
 * - The object will create a Json object and write all the public attributes plus all the values of the public method so that the name match a protected/private attribute
 * - The basic values are writen in a standard json format
 * 
 * The maximal depth of the json object is 20
 * 
 * @see \Library\Page\Page
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class PageJson extends Page {
	
	/**
	 * Function that creates the Json
	 * 
	 * @param mixed $mixed
	 * @param number $depth
	 * @return string
	 */
	protected function json_encode($mixed, $depth = 0) {
		$depth++;
		if ($depth > 20)
			return '"too long"';
		
		if (is_null($mixed))
			return 'null';
		if (is_resource($mixed)) {
			return "\"ressource\"";
		} elseif (is_array($mixed)) {
			$data = array();
			$keys = array_keys($mixed);
			$isNum = true;
			foreach ($keys AS $k)
				$isNum &= is_numeric($k);
			
			
			if (!$isNum) {
				if (is_object($index))
					$index = "object";
				
				foreach ($mixed as $index=>$mixedElement)
					if (!is_resource($mixedElement))
						$data[] = "\"$index\": " . $this->json_encode($mixedElement, $depth);
				
				return '{"entity" : {' . implode(", ", $data) . "}}";
			} else {
				foreach ($mixed as $index=>$mixedElement)
					if (!is_resource($mixedElement))
						$data[] = $this->json_encode($mixedElement, $depth);
				
				return '{"entity" : [' . implode(", ", $data) . "]}";
			}
		} elseif (is_object($mixed)) {
			$data = array();
			
			$class = new \ReflectionClass($mixed);
			$props = $class->getProperties(\ReflectionProperty::IS_PUBLIC);
				
			if ($class->getNamespaceName() != "Library") {
				
				foreach ($props AS $val) {
					$propName = $val->getName();
					
					$data[] = $this->json_encode($propName, $depth) . " : " . $this->json_encode($mixed->$propName, $depth);
				}
				
				$props = $class->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE);
				$meth = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
				
				foreach ($props AS $p) {
					try {
						$geter = $p->getName();
						
						$val = $mixed->$geter();
						
						$data[] = $this->json_encode($p->getName(), $depth) . " : " . $this->json_encode($val, $depth);
					} catch (\Exception $e) {
						try {
							$geter = "set" . ucfirst($p->getName());
							
							$val = $mixed->$geter();

							$data[] = $this->json_encode($p->getName(), $depth) . " : " . $this->json_encode($val, $depth);
						} catch (\Exception $e) {
							
						}
						
					}
				}
				
				return '{"' . $class->getShortName() . '" : {' . implode(", ", $data) . '}}';
			} else
				return '"Library data"';
			
		} else {
			if (is_bool($mixed) || is_string($mixed))
				$mixed = '"' . $mixed . '"';
			
			return $mixed;
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\Page\Page::generate()
	 */
	public function generate(){
		$this->app->httpResponse()->addHeader('Content-Type: application/json');
		
		return "" . $this->json_encode($this->vars) . ""; 
	}
}

?>