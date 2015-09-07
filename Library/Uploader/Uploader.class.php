<?php

namespace Library\Uploader;

if (!defined("EVE_APP"))
	exit();

class Uploader {
	protected $uppRoot;
	protected $maxSize;
	protected $validExt = array();
	
	protected $error = array();
	
	CONST INVALID_UPPROOT = 0;
	CONST INVALID_MAXSIZE = 1;
	CONST INVALID_VALIDEXT = 2;
	CONST INVALID_UPLOAD = 3;
	CONST INVALID_SIZ = 4;
	CONST INVALID_EXT = 5;
	
	public function uppRoot() {
		if (isset($this->uppRoot)) {
			return $this->uppRoot;
		} else {
			return null;
		}
	}
	
	protected function setUppRoot($pVal) {
		echo $pVal;
		if(!is_dir($pVal)) {
			echo "1";
		}
		
		if(!is_writable($pVal)) {
			echo "2";
		}
		
		if (empty($pVal) || !is_writable($pVal) || !is_dir($pVal)) {
			$this->setError($this::INVALID_UPPROOT);
			return 0;
		}
		$this->uppRoot = \Utils::protect($pVal);
		return 1;
	}
	
	public function maxSize() {
		if (isset($this->maxSize)) {
			return $this->maxSize;
		} else {
			return -1;
		}
	}
	
	public function setMaxSize($pVal) {
		if (is_numeric($pVal) && !empty($pVal)) {
			$this->maxSize = $pVal;
			return 1;
		}
		$this->setError($this::INVALID_MAXSIZE);
		return 0;
	}
	
	public function validExt() {
		if (isset($this->validExt)) {
			return $this->validExt;
		} else {
			return array();
		}
	}
	
	public function setValidExt($pVal) {
		if (is_array($pVal)) {
			$this->validExt = array_merge($this->validExt(), $pVal);
			return 1;
		} else if(is_string($pVal)) {
			$this->validExt[] = $pVal;
			return 1;
		} else {
			$this->setError($this::INVALID_VALIDEXT);
			return 0;
		}
	}
	
	public static function getInstance($pRoot, $pExt = array(), $pMaxSize = -1) {
		$upp = new Uploader();
		
		if($upp->setUppRoot($pRoot) && $upp->setMaxSize($pMaxSize) && $upp->setValidExt($pExt)) {
			return $upp;
		} else {
			return $upp->error();
		}
	}
	
	public function upload($pFile) {
		$file = basename($pFile['name']);
		$size = filesize($pFile['tmp_name']);
		
		$extension = strrchr($file, '.');
		
		if($this->validExt == array() || in_array($extension, $this->validExt)){
			if($this->maxSize < 0 || $size <= $this->maxSize){
			     $file = strtr($file, 
			          'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 
			          'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
			     $file = time() . '_' . preg_replace('/([^.a-z0-9]+)/i', '_', $file);
			     
			     if (move_uploaded_file($pFile['tmp_name'], $this->uppRoot . $file)) {
			     	return $file;
			     } else {
			     	$this->setError($this::INVALID_UPLOAD);
			     	return 0;
			     }
			} else {
				$this->setError($this::INVALID_SIZE);
			}
		} else {
			$this->setError($this::INVALID_EXT);
		}
		
		return 0;
	}
	
	protected function setError($pVal){
		if(!in_array($pVal, $this->error)) {
			$this->error[] = $pVal;
		}
	}
	
	public function error() {
		return $this->error;
	}
	
}

?>