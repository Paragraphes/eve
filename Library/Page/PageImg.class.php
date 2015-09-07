<?php

namespace Library\Page;

if (!defined("EVE_APP"))
	exit();

/**
 * Page that returns an image.
 * 
 * The needed attributes are
 * 
 * - imageX : the width of the image
 * - imageY : the height of the image
 * 
 * The optional attributes are
 * 
 * - imageFormat : the format of the image, by default will be PNG
 * - name : the name of the image, by default md5 of the time
 * 
 * It checks if the download folder is well based (it means that the root folder of the download is well the folder of download given in the application config file).
 * 
 * The image is an {@see \imagecreatetruecolor()} and the content file has to use functions that work on it. The name of the image variable is $image
 * 
 * The text content of the content file will be added on the image. If no text is needed on the image, then the content file should avoid to write anything.
 * 
 * The different possible format are JPG, PNG and GIF 
 * 
 * @see \Library\Page\Page
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class PageImg extends Page {
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\Page\Page::generate()
	 */
	public function generate(){
		if (!key_exists("imageX", $this->attribute) || !key_exists("imageY", $this->attribute))
			if (\Library\Application::appConfig()->getConst("LOG"))
				throw new \IllegalArgumentException("Error ID: " . \Library\Application::logger()->log("Error", "Page", "The image has to have a dimension (x and y)", __FILE__, __LINE__));
			else
				throw new \IllegalArgumentException("The image has to have a dimension (x and y)");
		
		if (key_exists("imageFormat", $this->attribute))
			$imageFormat = $this->attribute["imageFormat"];
		else
			$imageFormat = "PNG";
		
		if (key_exists("name", $this->attribute))
			$imageName = $this->attribute["name"];
		else
			$imageName = md5(time());
		
		extract($this->vars);
		$image = \imagecreatetruecolor($this->attribute["imageX"], $this->attribute["imageY"]);
		
		// Bloque le flux de sortie
		ob_start();
		if (file_exists($this->contentFile.".img.php"))
			require($this->contentFile."img.php");
		else
			require($this->contentFile.".php");
				
		// Place le contenu  de la vue dans la variable $content et débloque le flux de sortie
		$content = ob_get_clean();
		
		$text_color = imagecolorallocate($image, 88, 88, 88);
		imagestring($image, 1, 5, 5, $content, $text_color);
		
		switch (strtolower($imageFormat)) {
			case "jpeg":
			case "jpg":
				header('Content-Type: image/jpeg');
				imagejpeg($image);
				break;
			case "gif":
				header('Content-Type: image/gif');
				imagegif($image);
				break;
			case "png":
			default:
				header("Content-type: image/png");
				imagepng($image);
		}
		
		imagedestroy($image);
		
		return "";
	}
}

?>