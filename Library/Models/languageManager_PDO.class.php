<?php

namespace Library\Models;

if (!defined("EVE_APP"))
	exit();

class languageManager_PDO extends \Library\Manager_PDO implements languageManager {
	public function get( $pLang) {
		if (!($pLang instanceof \Library\Entities\Language))
			return null;
			
		$requete = $this->dao->prepare('SELECT
											`valeur`
										FROM
											`language`
										WHERE
											`clef` = :clef
										AND
											`lang` = :lang
										LIMIT 0, 1
										;');

		$requete->bindValue(':clef', $pLang->clef());
		$requete->bindValue(':lang', $pLang->lang());
		$requete->execute();


		$requete->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Library\Entities\Language');
		
		return $requete->fetch();
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