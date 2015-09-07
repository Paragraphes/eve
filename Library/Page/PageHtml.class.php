<?php

namespace Library\Page;

if (!defined("EVE_APP"))
	exit();

/**
 * Page that returns an HTML.
 * No attribute is needed
 * 
 * The optional attributes are
 * 
 * - template : Whether  we want to show the template or not.
 * 
 * It checks if the download folder is well based (it means that the root folder of the download is well the folder of download given in the application config file).
 * 
 * @see \Library\Page\Page
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class PageHtml extends Page {
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\Page\Page::generate()
	 */
	public function generate() {
		
		// Retourne les variables du tableau sous la forme 
		extract($this->vars);
		
		if(file_exists($this->contentFile.".php")){
			// Bloque le flux de sortie
			ob_start();
				
				require($this->contentFile.".php");
				
			// Place le contenu  de la vue dans la variable $content et débloque le flux de sortie
			$content = ob_get_clean();
		}
		
		// Bloque le flux de sortie
		ob_start();
		if (key_exists("template", $this->attribute) && $this->attribute["template"])
			require(__DIR__ . '/../../Applications/' . $this->app->name() . '/Templates/layout.php');
		else
			echo $content;
		
		// Retourne le contenu de la page
		return ob_get_clean();
	}
}

?>