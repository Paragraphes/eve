<?php

namespace Library\Entities;

if (!defined("EVE_APP"))
	exit();

use Library\Entity;

class user extends \Library\Entity {
	protected $id;
	protected $login;
	protected $password;
	protected $language;
	protected $email;
	protected $inscr_date;
	protected $civilite;
	protected $prenom;
	protected $nom;
	protected $no_tel;
	protected $password_conf;
	protected $admin;
	protected $reference_user;
	protected $prospectus_min_num_id;
	protected $sga_min_num_id;
	protected $fonction;
	protected $fax;
	protected $skype;
	protected $listeAdresse = array();
	
	protected $listeParam = array();
	
	protected $modif_pass;
	
	const INVALID_ID = 1;
	const INVALID_PASSWORD = 2;
	const INVALID_LOGIN = 10;
	const INVALID_LANGUAGE = 3;
	const INVALID_EMAIL = 4;
	const INVALID_ENTREPRISE = 5;
	const INVALID_PRENOM = 6;
	const INVALID_NOM = 7;
	const INVALID_ADRESSE = 8;
	const INVALID_LOCALITE = 9;
	const INVALID_FONCTION = 10;
	public function removePassword() {
		$this->password = "";
		
		return $this;
	}
	
	public function setPassword($pVal) {
		if (is_string($pVal) && !empty($pVal)) {
			$this->password = \Utils::hash($pVal, \Utils::getBlowfishSalt());
			$this->modif_pass = 1;
		} else {
			$this->modif_pass = 0;
		}
		
		return $this;
	}
	
	public function password() {
		if (isset($this->password)) {
			return $this->password;
		} else {
			return "";
		}
	}
	
	public function setPassword_conf($pVal) {
		if($this->modif_pass){
			if (\Utils::hash($pVal, $this->password) == $this->password) {
			} else {
				$this->setError(self::INVALID_PASSWORD);
			}
		}
		
		return $this;
	}
	
	public function password_conf() {
		return "";
	}
	
	public function language() {
		if (isset($this->language)) {
			return $this->language;
		} else {
			return self::$app->httpRequest()->languageUser();
		}
	}
	
	public function setLanguage($pVal) {
		if (is_string($pVal) && !empty($pVal)) {
			$this->language = \Utils::getFormatLanguage($pVal);
			return 1;
		}
		$this->setError(self::INVALID_LANGUAGE);
		return 0;
		
		return $this;
	}
	
	public function setEmail($pVal) {
		if (\Utils::testEmail($pVal)) {
			$this->email = $pVal;
			return 1;
		}
		$this->setError(self::INVALID_EMAIL);
		return 0;
		
		return $this;
	}
	
	public function adresse($pKey = 0) {
		if ($pKey >= 0 && $pKey < count($this->listeAdresse))
			return $this->listeAdresse[$pKey];
		else
			return $this->listeAdresse;
	}
	
	public function setAdresse(\Library\Entities\adresse $pAdresse, $pKey = -1) {
		$pAdresse->setUser($this);
		
		if ($pKey >= 0 && $pKey < count($this->listeAdresse))
			$this->listeAdresse[$pKey] = $pAdresse;
		else
			$this->listeAdresse[] = $pAdresse;
		
		return $this;
	}
	
	public function __get($name) {
		$obj = new \ReflectionClass($this);
		
		if ($obj->hasProperty($name) && isset($this->$name)) {
			return $this->$name();
		}
		
		return $this->getParam($name);
	}
	
	public function __set($name, $val) {

		$obj = new \ReflectionClass($this);
		
		if ($obj->hasProperty($name)) {
			$this->__call($name, $val);
		}
		
		$this-setParam($name, $val);
		
		return $this;
	}
	
	public function __call($name, $pVal) {
		$cAdresse = new \ReflectionClass(new \Library\Entities\adresses());
		$set = (strtolower(substr($name, 0, 3)) == "str");
		$prop = strtolower(($set) ? substr($name, 3) : $name);
			
		if ($set && count($listeAdresse == 0))
			return null;
		
		if ($cAdresse->hasProperty($prop)) {
			$key = ((!$set && count($pVal)) ? $pVal[0] : (($set && count($pVal) == 2) ? $pVal[1] : 0));
			
			if (key_exists($key, $listeAdresse))
				if ($set)
					return $listeAdresse->$name($pVal[0]);
				else
					return $listeAdresse->$name();
		}
		
		$cUser = new \ReflectionClass($this);
		
		if ($cUser->hasProperty($prop))
			return parent::__call($name, $pVal);
		
		if ($set)
			return $this->setAttr($pVal[0], $prop);
		else
			return $this->getAttr($prop);
		
	}
	
	public function setAttr($val, $name = null, $force = 1) {
		if (is_array($val) && $name == null)
			foreach ($val AS $key => $elem)
				$this->setAttr($key, $elem, $force);
		else
			if (!key_exists($name, $this->listeParam) || $force)
				$this->listeParam[$name] = \Utils::protect($val);
			
		return $this;
	}
	
	public function getAttr($name) {
		if (key_exists($name, $this->listeParam))
			return $this->listeParam[$name];
		else
			return "";
	}
	
	public function getListeParam() {
		return $this->listeParam;
	}
}

?>