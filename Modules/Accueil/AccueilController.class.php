<?php

namespace Modules\Accueil;

if (!defined("EVE_APP"))
	exit();

class Bidule {
	private $priv = 5;
	protected $prot = 3;
	public $pub = 1;
	public function action() {
		return true;
	}
}

class AccueilController extends \Library\BackController {
	public function executeIndex(\Library\HTTPRequest $request) {
		$languageManager = $this->managers->getManagersOf("language");
		
		//$this->page->addVar("test", $languageManager->getList(array("id < 3")));
		
		$test3 = (object) ["property"=>"blibli","test"=>12];
		$test1 = array("property"=>"blibli","test"=>12);
		$test2 = new \Library\Entities\config(array("clef" => "key_1", "valeur" => "val_1"));
		$test4 = new Bidule();
		$test5 = 15;
		
		$this->page()->addVar("array", $test1);
		$this->page()->addVar("entity", $test2);
		$this->page()->addVar("object", $test3);
		//$this->page()->addVar("custom", $test4);
		$this->page()->addVar("val", $test5);
		
		$this->page()->setIsXml();
	}
}

?>