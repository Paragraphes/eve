<?php

namespace Library\Page;

if (!defined("EVE_APP"))
	exit();

/**
 * Page that returns a file.
 * The needed attributes are
 * 
 * - rep : the repository attribute
 * - fileToLoad : the name of the file
 * 
 * It checks if the download folder is well based (it means that the root folder of the download is well the folder of download given in the application config file).
 * 
 * @see \Library\Page\Page
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class PageFile extends Page {
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\Page\Page::generate()
	 */
	public function generate(){
		if (!(key_exists("rep", $this->attribute) && key_exists("fileToLoad", $this->attribute)))
			if (\Library\Application::appConfig()->getConst("LOG"))
				throw new \IllegalArgumentException("Error ID: " . \Library\Application::logger()->log("Error", "Form", "You need a repo and a file to load data.", __FILE__, __LINE__));
			else
				throw new \IllegalArgumentException("You need a repo and a file to load data.");
		
		if (!file_exists($this->attribute["rep"] . $this->attribute["fileToLoad"]))
			$this->app()->httpResponse()->redirect404();
		
		$appConfig = $this->app->appConfig();

		if (!(stripos($this->attribute["rep"], \Library\Application::appConfig()->getConst("DOWNLOAD_FOLDER")) === 0))
			$this->app->httpResponse()->redirect403();
		
		$finfo = \finfo_open(FILEINFO_MIME_TYPE); // Retourne le type MIME à l'extension mimetype.
		$mime = finfo_file($finfo, $this->attribute["rep"] . $this->attribute["fileToLoad"]);
		finfo_close($finfo);
		
		header('Content-type: ' . $mime);
		
		header('Content-Disposition: attachment; filename="'.$this->attribute["fileToLoad"].'"');
		
		readfile($this->rep . $this->fileToLoad);
	}
}

?>