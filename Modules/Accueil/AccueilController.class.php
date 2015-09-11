<?php

namespace Modules\Accueil;

use Library\Entity;
if (!defined("EVE_APP"))
	exit();

class AccueilController extends \Library\BackController {
	public function executeIndex(\Library\HTTPRequest $request) {
		$languageManager = $this->managers->getManagersOf("language");
		
		$mgr1 = $languageManager->get(5);
		$mgrlist = $languageManager->getList(array("id < 3"));
		
		$this->page->addVar("local", rand());
		$this->page->addVar("id_simple", $mgr1);
		$this->page->addVar("id_liste", $mgrlist);
	}
	
	public function executeTest(\Library\HTTPRequest $request) {
		$this->page->addVar("exemple", "cette page ne contient qu'un JSON et n'a pas besoin de vue.");
		$this->page->setIsJson();
	}
}

?>