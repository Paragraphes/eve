<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * Class that returns the translation of a value given a key and a language.
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class Language {
	
	/**
	 * A specific manager used to get the data from saved value on the server.
	 * 
	 * @var \Library\Manager
	 */
	protected $manager = NULL;
	
	/**
	 * Constructor of the {@see \Library\Language} that needs to provide a {@see \Library\Manager} used to get the data from the server.
	 * 
	 * @see \Library\Manager
	 * 
	 * @param Manager $pManager
	 */
	public function __construct(Manager $pManager) {
		$this->manager = $pManager;
	}
	
	/**
	 * Returns the value of a key given a specific language.
	 * 
	 * Since the DAO is a subclass of {@see \Library\Manager}, it needs to use an instance of {@see \Library\Entity},
	 * so we provide it and call the standard {@see \Library\Manager::get()} with specified language.
	 * If it give no value, then we try with the default language.
	 * Finally, if the given key don't give any result, return null.
	 * 
	 * @param string $clef
	 * @param string $lang
	 * @return string|null
	 */
	public function get($clef, $lang){
		
		$lang = $this->manager->get(new \Library\Entities\language(array("clef" => $clef, "lang" => $lang)));
		
		if ($lang != null) {
			return $lang->valeur();
		} else {
			$lang = $this->manager->get(new \Library\Entities\language(array("clef" => $clef, "lang" => \Utils::defaultLanguage())));
			if ($lang != null)
				return $lang->valeur();
			else
				return null;
		}
	}
	
}

?>