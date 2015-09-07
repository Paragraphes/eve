<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * Subclass of {@see \Library\GrantAccess}.
 * 
 * This class checks whether a user is well connected and is allowed to continue on this route or not.
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class GrantAccess_PDO extends GrantAccess {
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\GrantAccess::checkAccess()
	 */
	public function checkAccess($pRoute, $pUser) {
		if (isset($_GET['deco']))
			throw new \Library\Exception\AccessException("Unconnect", \Library\Exception\AccessException::DECONEXION);
		
		$admin_lvl = 0;
		
		if($pRoute->admin_lvl() && $pUser !== NULL){
			
			$dao = PDOFactory::getMysqlConnexion();
			
			
			$sql = "
					SELECT
						MAX(admin_lvl) AS grant_access
					FROM
						(
						SELECT
							admin_lvl
						FROM
							user_access
						WHERE
							user_id = :pUserId
						AND
							route_id = :pRouteId
						UNION
						SELECT
							admin_lvl
						FROM
							groupe_access
						WHERE
							route_id = :pRouteId
						AND
							groupe_id IN (
								SELECT
									groupe_id
								FROM
									user_groupe
								WHERE
									user_id = :pUserId
							)
						UNION
						SELECT
							count(*) * " . \Library\Application::appConfig()->getConst("MAX_ADMIN_LVL") . " AS admin_lvl
						FROM
							user
						WHERE
							id = :pUserId
						AND
							admin >= :pAdminLvl
						) AS T1
					;";
			
			$requete = $dao->prepare($sql);
			
			$requete->bindValue(":pRouteId", $pRoute->id());
			$requete->bindValue(":pUserId", $pUser);
			$requete->bindValue(":pAdminLvl", \Library\Application::appConfig()->getConst("MAX_ADMIN_LVL"));
			
			$requete->execute();
			$info = $requete->fetch(\PDO::FETCH_ASSOC);
			
			$admin_lvl = $info["grant_access"];
			
			if ($admin_lvl == 0)
				throw new \Library\Exception\AccessException("Not allowed to go on this road", \Library\Exception\AccessException::NOT_ALLOWED);
			
			if (($duration = \Library\Application::appConfig()->getConst("INTERVAL_DURATION")) == null)
				$duration = 30;
			
			$sql = "
			SELECT
				id
			FROM
				connexion_log
			WHERE
				session_id = :session_id
			AND
				ip_adresse = :ip_adresse
			AND
				date_clk > (
							NOW() - INTERVAL " . $duration . " MINUTE
							)
			AND
				match_access = 1
			;";
			
			$requete = $dao->prepare($sql);
			
			$requete->bindValue(':session_id', session_id());
			$requete->bindValue(':ip_adresse', $_SERVER['REMOTE_ADDR']);
			
			$requete->execute();
			
			$infos = $requete->fetchAll(\PDO::FETCH_OBJ);
			
			if(count($infos) == 0)
				throw new \Library\Exception\AccessException("Unconnect", \Library\Exception\AccessException::TIME_FINISHED);
		
			$requete = $dao->prepare("UPDATE connexion_log SET date_clk = NOW() WHERE id = :id;");
			$requete->bindValue(':id', $infos[0]->id);
			
			$requete->execute();
		}
		
		return $admin_lvl;
	}
	
}

?>