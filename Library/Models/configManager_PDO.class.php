<?php

namespace Library\Models;

if (!defined("EVE_APP"))
	exit();

class configManager_PDO extends \Library\Manager_PDO implements configManager {
	
	const ERROR10100 = "Error 10100: Entity could not be found.";
	const ERROR10150 = "Error 10150: Cannot update entity.";
	const ERROR10155 = "Error 10155: Cannot insert entity.";
	const ERROR10160 = "Error 10160: Cannot send entity.";
	const ERROR10165 = "Error 10165: Cannot delete entity.";
	const ERROR10166 = "Error 10166: Cannot delete entities.";
	const ERROR10190 = "Error 10190: The key must be an entity.";
	
	public function get($clef){
		if (!($clef instanceof \Library\Entity))
			throw new \Library\Exception\PDOException(\Library\Application::logger()->log("Error", "PDO", self::ERROR10190, __FILE__, __LINE__), \Library\Exception\PDOException::INVALID_KEY);
		
		$returnId = 0;
		
		$query = $this->dao->prepare('SELECT id, valeur, clef FROM config WHERE clef = :clef LIMIT 0, 1;');
		
		$query->bindValue(':clef', \Utils::protect($clef->clef()), \PDO::PARAM_STR);
		
		$query->execute();
		
		$query->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Library\Entities\Language');
		
		$temp = $query->fetch();
		
		if (empty($temp) && func_num_args() >= 2 && !func_get_arg(1))
			throw new \Library\Exception\PDOException(\Library\Application::logger()->log("Error", "PDO", self::ERROR10100, __FILE__, __LINE__), \Library\Exception\PDOException::EMPTY_RESULT);
		
		return $temp;
	}
	
	public function getList(array $conditions = array(), array $param = array()) {
		return array();
	}
	
	public function update(\Library\Entity $pEntity) {
		throw new \Exception(\Library\Application::logger()->log("Error", "Model", self::ERROR10150, __FILE__, __LINE__));
	}
	
	public function insert(\Library\Entity $pEntity) {
		throw new \Exception(\Library\Application::logger()->log("Error", "Model", self::ERROR10155, __FILE__, __LINE__));
	}
	
	public function send(\Library\Entity $pEntity) {
		throw new \Exception(\Library\Application::logger()->log("Error", "Model", self::ERROR10160, __FILE__, __LINE__));
	}
	
	public function delete($pId) {
		throw new \Exception(\Library\Application::logger()->log("Error", "Model", self::ERROR10165, __FILE__, __LINE__));
	}
	
	public function deleteList(array $cond = array(), array $param = array()) {
		throw new \Exception(\Library\Application::logger()->log("Error", "Model", self::ERROR10166, __FILE__, __LINE__));
	}
	
}

?>