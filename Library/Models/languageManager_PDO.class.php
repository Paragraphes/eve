<?php

namespace Library\Models;

if (!defined("EVE_APP"))
	exit();

class languageManager_PDO extends \Library\Manager_PDO implements languageManager {
	
	const ERROR10200 = "Error 10200: Language could not be found.";
	const ERROR10250 = "Error 10250: Cannot update entity.";
	const ERROR10255 = "Error 10255: Cannot insert entity.";
	const ERROR10260 = "Error 10260: Cannot send entity.";
	const ERROR10265 = "Error 10265: Cannot delete entity.";
	const ERROR10266 = "Error 10266: Cannot delete entities.";
	const ERROR10290 = "Error 10290: The key must be a Language.";
	
	public function get($pLang) {
		if (!($pLang instanceof \Library\Entities\Language))
			throw new \Library\Exception\PDOException(\Library\Application::logger()->log("Error", "PDO", self::ERROR10290, __FILE__, __LINE__), \Library\Exception\PDOException::INVALID_KEY);
			
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
		
		$temp = $requete->fetch();
		
		if (empty($temp) && func_num_args() >= 2 && !func_get_arg(1))
			throw new \Library\Exception\PDOException(\Library\Application::logger()->log("Error", "PDO", self::ERROR10200, __FILE__, __LINE__), \Library\Exception\PDOException::EMPTY_RESULT);
		
		return $temp;
	}
	
	public function getList(array $conditions = array(), array $param = array()) {
		return array();
	}
	
	public function update(\Library\Entity $pEntity) {
		throw new \Exception(\Library\Application::logger()->log("Error", "Model", self::ERROR10250, __FILE__, __LINE__));
	}
	
	public function insert(\Library\Entity $pEntity) {
		throw new \Exception(\Library\Application::logger()->log("Error", "Model", self::ERROR10255, __FILE__, __LINE__));
	}
	
	public function send(\Library\Entity $pEntity) {
		throw new \Exception(\Library\Application::logger()->log("Error", "Model", self::ERROR10260, __FILE__, __LINE__));
	}
	
	public function delete($pId) {
		throw new \Exception(\Library\Application::logger()->log("Error", "Model", self::ERROR10265, __FILE__, __LINE__));
	}
	
	public function deleteList(array $cond = array(), array $param = array()) {
		throw new \Exception(\Library\Application::logger()->log("Error", "Model", self::ERROR10266, __FILE__, __LINE__));
	}
	
}

?>