<?php

namespace Library\Entities;

if (!defined("EVE_APP"))
	exit();

class language extends \Library\Entity {
	protected $id;
	protected $clef;
	protected $valeur;
	protected $lang;

	CONST INVALID_ID = 1;
	CONST INVALID_CLEF = 1;
	CONST INVALID_VALEUR = 2;
	CONST INVALID_LANG = 3;
}

?>