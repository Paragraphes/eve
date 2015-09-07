<?php

namespace Library\Models;

if (!defined("EVE_APP"))
	exit();

use Library\Models\AdresseManager;

class adresseManager_PDO extends \Library\Manager_PDO implements adresseManager {
	
	public function getAdresseForUser($pId) {
		$query = $this->dao->prepare('
				SELECT
					id,
					user_id,
					entreprise,
					adresse,
					case_postale,
					localite
				FROM
					adresses
				WHERE
					(
						user_id = :user_id
					OR
						user_id IN (
								SELECT
									id
								FROM
									user
								WHERE
									reference_user = :user_id
								)
					)
				');
		
		$query->bindValue(":user_id", $pId, \PDO::PARAM_INT);
		
		$query->execute();
		
		$query->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Library\Entities\Adresse');
		
		return $query->fetchAll();
	}
	
	public function get($pId) {
		$query = $this->dao->prepare('
				SELECT
					id,
					user_id,
					entreprise,
					adresse,
					case_postale,
					localite
				FROM
					adresses
				WHERE
					id = :id
				');
		
		$query->bindValue(":id", $pId, \PDO::PARAM_INT);
		
		$query->execute();
		
		$query->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Library\Entities\Adresse');
		
		return $query->fetch();
	}
	
	public function allowedAdresse($userId, $adresseId) {
		$query = $this->dao->prepare("
				SELECT
					COUNT(*) AS test
				FROM
					adresses
				WHERE (
						user_id = :user_id
					OR
						user_id IN (
								SELECT
									id
								FROM
									user
								WHERE
									reference_user = :user_id
								)
					)
				AND
					id = :adresse_id
				;");

		$query->bindValue(":user_id", $userId, \PDO::PARAM_INT);
		$query->bindValue(":adresse_id", $adresseId, \PDO::PARAM_INT);
		
		$query->execute();
		
		$adresse = $query->fetch(\PDO::FETCH_ASSOC);
		
		return $adresse["test"] >= 1;
	}
		
	public function send(\Library\Entity $pAdresse) {
		if ($pAdresse->id() != 0) {
			return $this->updateAdresse($pAdresse);
		} else {
			return $this->insertAdresse($pAdresse);
		}
	}
	
	public function insert(\Library\Entity $pAdresse) {
		$query = $this->dao->prepare("
				INSERT INTO
					adresses (id, user_id, entreprise, adresse, case_postale, localite)
				VALUES (
					null,	
					:user_id,
					:entreprise,
					:adresse,
					:case_postale,
					:localite
				);");

		$query->bindValue(":user_id", $pAdresse->user_id(), \PDO::PARAM_INT);
		$query->bindValue(":entreprise", $pAdresse->entreprise());
		$query->bindValue(":adresse", $pAdresse->adresse());
		$query->bindValue(":case_postale", $pAdresse->case_postale());
		$query->bindValue(":localite", $pAdresse->localite());
		
		if ($query->execute()) {
			$pAdresse->setId($this->dao->lastInsertId());
			return $pAdresse;
		} else {
			return null;
		}
		
	}
	
	public function update(\Library\Entity $pAdresse) {
		$query = $this->dao->prepare("
				UPDATE
					adresses
				SET
					user_id = :user_id,
					entreprise = :entreprise,
					adresse = :adresse,
					case_postale = :case_postale,
					localite = :localite
				WHERE
					id = :id
				;");

		$query->bindValue(":user_id", $pAdresse->user_id());
		$query->bindValue(":entreprise", $pAdresse->entreprise());
		$query->bindValue(":adresse", $pAdresse->adresse());
		$query->bindValue(":case_postale", $pAdresse->case_postale());
		$query->bindValue(":localite", $pAdresse->localite());
		$query->bindValue(":id", $pAdresse->id());
		
		if ($query->execute()) {
			return $pAdresse;
		} else {
			return null;
		}
	}
	
	public function getFirstAdresse($pId) {
		$query = $this->dao->prepare('
				SELECT
					id,
					user_id,
					entreprise,
					adresse,
					case_postale,
					localite
				FROM
					adresses
				WHERE
					user_id = :id
				');
		
		$query->bindValue(":id", $pId, \PDO::PARAM_INT);
		
		$query->execute();
		
		$query->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Library\Entities\Adresse');
		
		$adresse = $query->fetch();
		
		if ($adresse != null && $adresse instanceof \Library\Entities\Adresse) {
			return $adresse;
		} else {
			return new \Library\Entities\Adresse();
		}
	}
}

?>