<?php

namespace Library\Entities;

if (!defined("EVE_APP"))
	exit();

class config extends \Library\Entity{
	protected $id;
	protected $clef;
	protected $valeur;

	CONST INVALID_ID = 1;
	CONST INVALID_KEY = 1;
	CONST INVALID_VALUE = 2;
}

?>