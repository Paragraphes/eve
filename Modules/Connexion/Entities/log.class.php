<?php

namespace Modules\Connexion\Entities;

if (!defined("EVE_APP"))
	exit();

class log extends \Library\Entity{
	protected $login;
	protected $password;
	
	CONST INVALID_LOGIN = 1;
	
	public function setLogin($pVal){
		if(empty($pVal)){
			$this->errors[] = self::INVALID_LOGIN;
			return 0;
		}else{
			$this->login = \Utils::protect($pVal);
			return 1;
		}
	}
	
	public function setPassword($pVal){
		$this->password = \Utils::protect($pVal);
		return 1;
	}
	
	public function login(){
		return $this->login;
	}
	
	public function password(){
		return $this->password;
	}
}

?>