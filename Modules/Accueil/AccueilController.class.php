<?php

namespace Modules\Accueil;

if (!defined("EVE_APP"))
	exit();

class AccueilController extends \Library\BackController {
	public function executeIndex(\Library\HTTPRequest $request) {
		$languageManager = $this->managers->getManagersOf("language");
		
		$this->page->addVar("test", $languageManager->getList(array("id < 3")));
	}
}

?>