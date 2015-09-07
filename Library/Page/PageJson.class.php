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
 * - The object will create a Json object and write all the public attributes plus all the values of the public method
 *   so that the name match a protected/private attribute
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
	
	const MAX_DEPTH = 20;
	
	protected $valid = 1;
	protected $errors = array();
	
	/**
	 * Function that creates the Json
	 * 
	 * @param mixed $mixed
	 * @param number $depth
	 * @return string
	 */
	protected function json_encode($mixed, $depth = 0) {
		$depth++;
		if ($depth > self::MAX_DEPTH) {
			$this->valid = 0;
			$this->errors[] = "Maximum depth reached";
			return '"too deep"';
		}
		
		if (is_null($mixed))
			return 'null';
		if (is_resource($mixed)) {
			return '"resource"';
		} elseif (is_array($mixed)) {
			$data = array();
			$keys = array_keys($mixed);
			$isNum = true;
			$i = 0;
			foreach ($keys AS $k) {
				if ($i++ != $k) {
					$isNum = false;
					break;
				}
			}
			
			if (!$isNum) {
				if (is_object($k))
					$index = "object";
				
				foreach ($mixed as $index=>$mixedElement)
					if (!is_resource($mixedElement))
						$data[] = "\"$index\": " . $this->json_encode($mixedElement, $depth);
				
				return '{' . implode(", ", $data) . "}";
			} else {
				foreach ($mixed as $index=>$mixedElement)
					if (!is_resource($mixedElement))
						$data[] = $this->json_encode($mixedElement, $depth);
				
				return '[' . implode(", ", $data) . "]";
			}
		} elseif (is_object($mixed)) {
			$data = array();
			
			$class = new \ReflectionObject($mixed);
			$props = $class->getProperties(\ReflectionProperty::IS_PUBLIC);
			
			if ($class->getNamespaceName() != "Library") {
				
				foreach ($props AS $p) {
					$pname = $p->getName();
					$data[] = '"' . $pname . '" : ' . $this->json_encode($mixed->$pname, $depth);
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
						
						$data[] = '"' . $pname . '" : ' . $this->json_encode($val, $depth);
					} catch (\Exception $e) {
						$this->valid = 0;
						$this->errors[] = "Exception while getting property '$pname'";
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
		return "" . $this->json_encode(array("content"=>$this->vars, "valid"=>$this->valid, "errors"=>array_unique($this->errors))) . "";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\Page\Page::generate403()
	 */
	public function generate403() {
		$this->app->httpResponse()->addHeader('Content-Type: application/json');
		return "" . $this->json_encode(array("content"=>array(),"valid"=>0, "errors"=>array("403 - connection lost"))) . "";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\Page\Page::generate404()
	 */
	public function generate404() {
		$this->app->httpResponse()->addHeader('Content-Type: application/json');
		return "" . $this->json_encode(array("content"=>array(),"valid"=>0, "errors"=>array("404 - connection lost"))) . "";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\Page\Page::generateDefaultError()
	 */
	public function generateDefaultError() {
		$this->app->httpResponse()->addHeader('Content-Type: application/json');
		return "" . $this->json_encode(array("content"=>array(),"valid"=>0, "errors"=>array("connection lost"))) . "";
	}
}

?>