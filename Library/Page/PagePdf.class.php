<?php

namespace Library\Page;

if (!defined("EVE_APP"))
	exit();

/**
 * Page that returns a PDF.
 * 
 * No attribute is needed
 * 
 * The optional attributes are
 * 
 * - pdfSize : The size of the PDF page
 * - pdfFont : An array with the used font
 * 
 * It will generate a PDF file
 * 
 * The file will catch the data of the content file and add them to the PDF file.
 * 
 * It will create an instance of {@see \Library\Pdf\html2pdf} to create the file, so the different HTML value has to be valid for HTML2PDF.
 * 
 * @see \Library\Page\Page
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class PagePdf extends Page {
	
	const ERROR1050 = "Error 1050: The given view does not exist.";
	
	/**
	 * (non-PHPdoc)
	 * @see \Library\Page\Page::generate()
	 */
	public function generate(){
		if (!file_exists($this->contentFile.".php") && !file_exists($this->contentFile."_pdf.php"))
			throw new \RuntimeException(\Library\Application::logger()->log("Error", "Page", self::ERROR1050, __FILE__, __LINE__));
		

		if (key_exists("pdfSize", $this->attribute))
			$pdfSize = $this->attribute["pdfSize"];
		else
			$pdfSize = "A4";
		
		if (key_exists("pdfFont", $this->attribute))
			$fonts = $this->attribute["pdfFont"];
		else
			$fonts = array(); 
		
		extract($this->vars);
		
		ob_start();
		
		require('Library/Pdf/html2pdf.class.php');
		
		$pdf = new \HTML2PDF('P', $this->pdfSize,'fr', true, 'UTF-8', array(0, 0, 0, 0));
		
		foreach ($fonts AS $f) {
			if (key_exists("name", $f) && key_exists("link", $f)){
				
				if (key_exists("type", $f))
					$type = $f["type"];
				else
					$type = "";
				
				$pdf->addFont($f["name"], $type, $f["link"]);	
			}
		}
		
		if(file_exists($this->contentFile."_pdf.php")){
			require($this->contentFile."_pdf.php");
		}else {
			require($this->contentFile.".php");
		}
		
		$pdf->WriteHTML(ob_get_clean());
		
		$pdf->Output();
	}	
}

?>