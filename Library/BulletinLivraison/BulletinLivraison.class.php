<?php

namespace Library\BulletinLivraison;

if (!defined("EVE_APP"))
	exit();

/**
 * A class that generates preformated bulletins
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class BulletinLivraison {
	
	/**
	 * The users's adress
	 * 
	 * @var \Library\Entities\Adresse
	 */
	protected $adresse;
	
	/**
	 * The contact in ParaGP
	 * 
	 * @var \Library\Entities\Adresse
	 */
	protected $contact;
	
	/**
	 * Facturation number
	 * 
	 * @var int
	 */
	protected $no;
	
	/**
	 * Facturation date
	 * 
	 * @var \DateTime
	 */
	protected $date_facturation;
	
	/**
	 * Item list of livrason bulletin
	 * 
	 * @var \Library\BulletinLivraison\BulletinLivraisonElement[]
	 */
	protected $designation = array();
	
	/**
	 * Application root
	 * @var string
	 */
	protected $root;
	
	/**
	 * Language used for the bulletin
	 * 
	 * @var string
	 */
	protected $language;
	
	/**
	 * error list
	 * 
	 * @var String[]
	 */
	protected $error = array();

	/**
	 * TVA number of ParaGP
	 * 
	 * @var string
	 */
	CONST TVA_NUM = "CHE-325.284.944";
	
	CONST INVALID_TIME_TO_PAY = 0;
	CONST INVALID_NO_TVA = 1;
	CONST INVALID_CAMPDATE = 2;
	CONST INVALID_CLIENT_NO = 3;
	CONST INVALID_NO = 4;
	CONST INVALID_ADRESSE = 5;
	CONST INVALID_LANGUAGE = 6;
	CONST INVALID_NO_COMPTE = 7;
	
	/**
	 * Setter of the root
	 * @param string $pVal
	 * @return int
	 */
	public function setRoot($pVal) {
		if (is_string($pVal) && !empty($pVal)) {
			$this->root = \Utils::protect($pVal);
		}
		return 1;
	}
	
	/**
	 * Getter of the root
	 * @return string
	 */
	public function root() {
		if (isset($this->root)) {
			return $this->root;
		} else {
			return "";
		}
	}
	
	/**
	 * Getter of the address
	 * 
	 * @return \Library\Entities\Adresse|null
	 */
	public function adresse() {
		if (isset($this->adresse)) {
			return $this->adresse;
		} else {
			return null;
		}
	}
	
	/**
	 * Setter of the address
	 * 
	 * @param \Library\Entities\Adresse $pAdresse
	 * @return int
	 */
	public function setAdresse(\Library\Entities\Adresse $pAdresse) {
		$this->adresse = $pAdresse;
		return 1;
	}
	
	/**
	 * Getter of the language
	 *  
	 * @return string
	 */
	public function language() {
		if (isset($this->language)) {
			return $this->language;
		} else {
			return \Utils::defaultLanguage();
		}
	}
	
	/**
	 * Setter of the language
	 * 
	 * @param string $pVal
	 * @return int
	 */
	public function setLanguage($pVal) {
		if (is_string($pVal) && !empty($pVal)) {
			$this->language = \Utils::getFormatLanguage($pVal);
			return 1;
		}
		$this->setError(self::INVALID_LANGUAGE);
		return 0;
	}
	
	/**
	 * getter for the contact
	 * 
	 * @return \Library\Entities\Adresse
	 */
	public function contact() {
		if (isset($this->contact)) {
			return $this->contact;
		} else {
			return null;	
		}
	}
	
	/**
	 * setter for the contact
	 * 
	 * @param \Library\Entities\Adresse $pVal
	 * @return int
	 */
	public function setContact(\Library\Entities\Adresse $pVal) {
			$this->contact = $pVal;
			return 1;
	}
	
	/**
	 * Getter for the number
	 * 
	 * @return int
	 */
	public function no() {
		if (isset($this->no)) {
			return $this->no;
		} else {
			return -1;
		}
	}
	
	/**
	 * Setter for the number
	 * 
	 * @param int $pVal
	 * @return int
	 */
	public function setNo($pVal) {
		if (is_numeric($pVal) && !empty($pVal)) {
			$this->no = $pVal;
			return 1;
		}
		$this->setError(self::INVALID_NO);
		return 0;
	}
	
	/**
	 * Getter ot the facturation date
	 * 
	 * @return \DateTime
	 */
	public function date_facturation() {
		
		if (isset($this->date_facturation)) {
			if (! ($this->date_facturation instanceof \DateTime))
				$this->date_facturation = new \DateTime($this->date_facturation);
				
			return $this->date_facturation;
		} else {
			return new \DateTime();
		}
	}
	
	/**
	 * Setter of the facturation date
	 * 
	 * @param \DateTime $pVal
	 * @return int
	 */
	public function setDate_facturation($pVal) {
	
		if(is_array($pVal)){
			global $app;
			$langFormat = \Utils::getDateFormat($app->httpRequest()->languageGet());
			$date = $pVal[0].'-'.$pVal[1].'-'.$pVal[2];
				
			if(preg_match('/'.$langFormat[2].'/', $date)){
				$this->date_facturation = \DateTime::createFromFormat($langFormat[1], $date);
			} else {
				$this->errors[] = $this::INVALID_CAMPDATE;
				return 0;
			}
		}else if($pVal instanceof \DateTime){
			$this->date_facturation = $pVal;
		}else{
			$this->date_facturation = new \DateTime($pVal);
		}
	
		return 1;
	}
	
	/**
	 * Getter of the designation list
	 * 
	 * @return \Library\BulletinLivraison\BulletinLivraisonElement[]
	 */
	public function designation() {
		if (isset($this->designation)) {
			return $this->designation;
		} else {
			return null;
		}
	}
	
	/**
	 * Setter of the designation list
	 * 
	 * @param array|\Library\BulletinLivraisonElement $pVal
	 * @return int
	 */
	public function setDesignation($pVal) {
		if (is_array($pVal)) {
			$t = 1;
			foreach ($pVal AS $val) {
				$t *= $this->addDesignation($val);
			}
		} else {
			$t = $this->addDesignation($pVal);
		}
		
		return $t;
	}
	
	/**
	 * Adds a designation at the end of the designation list
	 * 
	 * @param \Library\BulletinLivraisonElement $pVal
	 * @return int
	 */
	public function addDesignation(\Library\BulletinLivraisonElement $pVal) {
		$this->designation[] = $pVal;
		return 1;
	}
	
	/**
	 * Returns whether there is an error or not
	 * @return boolean
	 */
	public function isError() {
		return count($this->error) != 0;
	}
	
	/**
	 * Returns the error list
	 * 
	 * @return string[]
	 */
	public function error() {
		return $this->error();
	}
	
	/**
	 * If the error doesn't already exist in the error list, adds the current error at the end of the list
	 * 
	 * @param string $pVal
	 */
	public function setError($pVal) {
		if (!in_array($pVal, $this->error))
			$this->error[] = $pVal;
	}
	
	/**
	 * Generates the bulletin
	 * 
	 * It will generate the bulletin in a \HTML2PDF way
	 * 
	 * @param string $firstInfo
	 * @param string $lastInfo
	 * @return string
	 */
	public function generate($firstInfo = '', $lastInfo = '') {
		
		$formatLanguage = \Utils::getDateFormat(\Utils::getFormatLanguage($this->language()));
		
		$ret = '<page format="A4">';
		
		$ret .= $firstInfo;
		
		$ret .= '<div style="position: absolute; top: 140px; left: 490px;">'
					. "99.60.061255.".$this->no()
				. '</div>';
		
		$ret .= '<div style="position:absolute;top:160px;left:450px;width:200px;font-size:17px;">';
			$ret .= '<div>' . $this->adresse() . '</div>';
		$ret .= '</div>';
		
		$ret .= '<div style="position: absolute; top: 330px; left: 8%; font-size: 16px; font-weight: bold;">';
			$ret .= "Bulletin de livraison N° PLO2." . $this->no();
		$ret .= '</div>';
		
		$ret .= '<div style="position: absolute; top: 330px; left: 450px; font-size: 14px;">';
			$ret .= 'Neuchâtel, le ' . \Utils::formatDate($this->date_facturation(), $formatLanguage[1]);
		$ret .= '</div>';
		
		$ret .= '<div style="position:absolute;left:5%;width:90%;top:430px;">';
			$ret .= '<div style="width:100%">';
			$ret .= '<table style="width:100%;border:0px;border-top:1px solid black;border-bottom:1px solid black;">';
				$ret .= '<tr>';
					$ret .= '<td style="width:70%">';
						$ret .= DESIGNATION;
					$ret .= '</td>';
					$ret .= '<td style="width:30%; text-align: right;">';
						$ret .= NOMBRE;
					$ret .= '</td>';
				$ret .= '</tr>';
			$ret .= '</table>';
			$ret .= '</div>';
			foreach ($this->designation AS $desc) {
				$ret .= '<div style="position:relative;width:100%;margin-left:20%;">' . $desc->generate() . '</div>';
			}
		$ret .= '</div>';
		
		$ret .= '<div style="width: 100px; height: 100px; border: 1px solid #bbbbbb; position: absolute; bottom: 50px; left: 50px;">'
				. '<div style="position: absolute; top: -7px; left: 5px; width: 65px; background-color: #FFFFFF; text-align: center;">'
					. COLIS_NO
				. '</div>'
				. '<div style="position: absolute; bottom: 5px; font-size: 20px; font-weight: bold; left: 25px;">'
					. COLIS
				. '</div>'
			. '</div>';
		
		$ret .= '<div style="width: 100px; height: 100px; border: 1px solid #bbbbbb; position: absolute; bottom: 50px; left: 200px;">'
				. '<div style="position: absolute; top: -7px; left: 5px; width: 75px; background-color: #FFFFFF; text-align: center;">'
					. TOTAL_SEND
				. '</div>'
				. '<div style="position: absolute; bottom: 5px; font-size: 20px; font-weight: bold; left: 25px;">'
					. COLIS
				. '</div>'
			. '</div>';
		
		$ret .= '<div style="width: 400px; height: 100px; border: 1px solid #bbbbbb; position: absolute; bottom: 50px; left: 350px;">'
				. '<div style="position: absolute; top: 20px; left: 20px;">'
					. DATE_SEND
				. '</div>'
				. '<div style="position: absolute; top: 20px; left: 150px;">'
					. \Utils::formatDate(new \DateTime(), $formatLanguage[1])
				. '</div>'
				. '<div style="position: absolute; bottom: 20px; left: 20px;">'
					. SIGNATURE
				. '</div>'
				. '<div style="position: absolute; bottom: 22px; left: 100px; border-bottom: 1px solid #bbbbbb; width: 270px; height: 5px;">'
				. '</div>'
			. '</div>';
		
		return $ret . '</page>';
	}
}

?>