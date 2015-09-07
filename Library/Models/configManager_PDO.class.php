<?php

namespace Library\Models;

if (!defined("EVE_APP"))
	exit();

class configManager_PDO extends \Library\Manager_PDO implements configManager {
	
	public function get($clef){
		if (!($clef instanceof \Library\Entity))
			return null;
		
		$returnId = 0;
		
		$query = $this->dao->prepare('SELECT id, valeur, clef FROM config WHERE clef = :clef LIMIT 0, 1;');
		
		$query->bindValue(':clef', \Utils::protect($clef->clef()), \PDO::PARAM_STR);
		
		$query->execute();
		
		$query->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Library\Entities\Language');
		
		return $query->fetch();
	}
	
	public function getList(array $conditions = array(), array $param = array()) {
		return array();
	}
	
	public function update(\Library\Entity $pEntity) {
		return false;
	}
	
	public function insert(\Library\Entity $pEntity) {
		return false;
	}
	
	public function send(\Library\Entity $pEntity) {
		return false;
	}
	
	public function delete($pId) {
		return false;
	}
	
	public function deleteList(array $cond = array(), array $param = array()) {
		return false;
	}
	
}

?>