<?php

namespace Modules\Accueil\Entities;

if (!defined("EVE_APP"))
	exit();

class language extends \Library\Entity {
	protected $id;
	protected $clef;
	protected $lang;
	protected $valeur;
	//protected $hippopotame;
	//protected $hippocampe;
}

?>