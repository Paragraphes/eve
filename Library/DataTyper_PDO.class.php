<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * Subclass of DataType using PDO
 * 
 * @see \Library\DataTyper_Manager
 *
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class DataTyper_PDO extends DataTyper_Manager {
	
	/**
	 * An optimized value that gives back the type if it has already be checked. Avoid multiple call on the DAO.
	 * 
	 * @var String[][]
	 */
	protected $listType = array();
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\DataTyper_Manager::getDataType()
	 */
	public function getDataType($module, $model) {
		if (!(key_exists($module, $this->listType) && key_exists($model, $this->listType[$module]))) {
			$dao = PDOFactory::getConnexion(); //TODO: use manager?
			
			$tableName = ($module != null) ? strtolower($module) . "_" . $model : $model;
			
			try {
				$query = $dao->prepare("DESCRIBE " . $tableName);
				
				$query->execute();
			} catch (\Exception $e) {
				$query = $dao->prepare("DESCRIBE " . $model);
				
				$query->execute();
			}
			$rows = $query->fetchAll(\PDO::FETCH_ASSOC);
			
			foreach ($rows AS $r) {
				$prop = array();
				
				$prop["null"] = $r["Null"];
				$prop["type"] = $r["Type"];
				$prop["default"] = $r["Default"];
				$prop["key"] = $r["Key"];
				
				$info[$r["Field"]] = $prop;
			}
			
			$this->listType[$module][$model] = $info;
			
		}
		
		return $this->listType[$module][$model];
	}
}

?>