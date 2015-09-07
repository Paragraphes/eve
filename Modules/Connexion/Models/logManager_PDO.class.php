<?php

namespace Modules\Connexion\Models;

if (!defined("EVE_APP"))
	exit();

class logManager_PDO extends \Library\Manager_PDO implements logManager {
	
	public function checkAcces(\Modules\Connexion\Entities\log $connexion, $pSessId, $pIp){
		/*
		$query = $this->dao->prepare('(SELECT COUNT(*) AS nbr FROM connexion_log WHERE ip_adresse = :pIp AND date_co >= date(NOW() - INTERVAL 1 HOUR) AND match_access = 0);');
		$query->bindValue(':pIp', $pIp);
		
		$query->execute();
		
		$info = $query->fetch(\PDO::FETCH_OBJ);
		
		if($info->nbr >= 3){
			return -1;
		}
		
		$query->fetch();//*/
		
		$returnId = 0;
		
		$query = $this->dao->prepare('SELECT id, password FROM user WHERE login = :login;');
		
		$query->bindValue(':login', $connexion->login());
		
		$query->execute();
		
		$list = $query->fetchAll(\PDO::FETCH_OBJ);
		
		foreach($list AS $elem){
			if(\Utils::hash($connexion->password(), $elem->password) == $elem->password){
				$returnId = $elem->id;
			}
		}
		
		$query = $this->dao->prepare('INSERT INTO connexion_log (id, session_id, ip_adresse, match_access, user_name, date_co, date_clk) VALUES (NULL, :pSessId, :pIp, :pMatch, :pUserName, NOW(), NOW());');
		
		$query->bindValue(':pSessId', $pSessId);
		$query->bindValue(':pIp', $pIp);
		if($returnId){
			$query->bindValue(':pMatch', 1);
		}else{
			$query->bindValue(':pMatch', 0);
		}
		
		$login = $connexion->login();
		if(!empty($login)){
			$query->bindValue(':pUserName', $connexion->login());
		}else{
			$query->bindValue(':pUserName', null);
		}
		
		$query->execute();
		
		return $returnId;
	}
	
	public function get($pId) {
		return null;
	}
	
	public function getList(array $cond = array(), array $param = array()) {
		return array();
	}
	
	public function send(\Library\Entity $pEntity) {
		return 0;
	}
	
	public function insert(\Library\Entity $pEntity) {
		return 0;
	}
	
	public function update(\Library\Entity $pEntity) {
		return 0;
	}
	
	public function delete($pId) {
		return 0;
	}
	
	public function deleteList(array $conditions = array(), array $param = array()) {
		return null;
	}
	
}

?>