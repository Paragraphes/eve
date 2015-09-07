<?php

namespace Library\Entities;

if (!defined("EVE_APP"))
	exit();

use Library\Entity;

class adresses extends \Library\Entity {
	protected $id;
	protected $user_id;
	protected $entreprise;
	protected $rue;
	protected $case_postale;
	protected $localite;
	protected $code_postal;
	protected $no_rue;
	
	protected $user;
	
	const INVALID_ID = 1;
	const INVALID_USER_ID = 2;
	const INVALID_ENTREPRISE = 3;
	const INVALID_CASE_POSTALE = 4;
	const INVALID_LOCALITE = 5;
	const INVALID_CODE_POSTAL = 6;
	const INVALID_NO_RUE = 7;
	
	public function nom() {
		return $this->user()->nom();
	}
	
	public function prenom() {
		return $this->user()->prenom();
	}
	
	public function titre() {
		return $this->user()->titre();
	}
	
	public function __toString(){
		$ret = '<div class="adresse">';

			if (isset($this->entreprise)) {
				$ret .= "<div>" . $this->entreprise . "</div>";
			}
			
			if ($this->titre() != "") {
				$ret .= "<div>" . $this->titre() . " " . $this->prenom() . " " . $this->nom() . "</div>";
			} else {
				$ret .= "<div>" . $this->prenom() . " " . $this->nom() . "</div>";
			}
			
			if (isset($this->adresse) && isset($this->no_rue)) {
				$ret .= "<div>" . $this->adresse . " " . $this->no_rue . "</div>";
			}
			if (isset($this->case_postale)) {
				$ret .= "<div>" . ((is_numeric($this->case_postale) ? "CP " . $this->case_postale : $this->case_postale)) . "</div>";
			}
			if (isset($this->localite) && isset($this->code_postal)) {
				$ret .= "<div>" . $this->code_postal . " " . $this->localite . "</div>";
			}
		
		$ret .= '</div>';
		
		return $ret;
	}
	
	public function setUser(\Library\Entities\user $pUser) {
		$this->user = $pUser;
		
		return $this;
	}
	
	public function user() {
		if (isset($this->user))
			return $this->user;
		else
			return new \Library\Entities\user();
	}
	
	public function setInfoFromUser(\Library\Entities\User $pUser) {
		$this->setNom($pUser->nom());
		$this->setPrenom($pUser->prenom());
		$this->setTitre($pUser->civilite());
		$this->setUser_id($pUser->id());
		
		return $this;
	}
}

?>