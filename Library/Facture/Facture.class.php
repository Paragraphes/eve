<?php

namespace Library\Facture;

if (!defined("EVE_APP"))
	exit();

use \Library\Facture\Bvr;

/**
 * A class that generates preformated facturation.
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class Facture {
	
	/**
	 * The client address
	 * 
	 * @var \Library\Entities\Adresse
	 */
	protected $adresse;
	
	/**
	 * The address of ParaGP
	 * 
	 * @var \Library\Entities\Adresse
	 */
	protected $contact;
	
	/**
	 * Name of the shop
	 * 
	 * @var string
	 */
	protected $magasFor;
	
	/**
	 * Id of the facturation
	 * 
	 * @var number
	 */
	protected $no;
	
	/**
	 * Id of the client
	 * 
	 * @var number
	 */
	protected $clien_no;

	/**
	 * Date of facture creation
	 * @var \DateTime
	 */
	protected $date_facturation;
	
	/**
	 * number of the bank account
	 * 
	 * @var string
	 */
	protected $noCompte;
	
	/**
	 *TVA number of the facturation
	 * 
	 * @var string
	 */
	protected $noTva = self::TVA_NUM;
	
	/**
	 * Item designation list
	 * @var \Library\Facture\FactureElement
	 */
	protected $designation = array();
	
	/**
	 * Time allowed to pay the facturation
	 * 
	 * @var int
	 */
	protected $time_to_pay = 30;
	
	/**
	 * language of the facturation
	 * @var string
	 */
	protected $language;
	
	/**
	 * error list
	 * @var string[]
	 */
	protected $error = array();
	
	/**
	 * address of ParaGP
	 * @var \Library\Entities\Adresse
	 */
	protected $factAdresse;

	/**
	 * TVA number of ParaGP
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
	 * getter of the shop
	 * @return string
	 */
	public function magasFor() {
		if (isset($this->magasFor)) {
			return $this->magasFor;
		} else {
			return "";
		}
	}
	
	/**
	 * Setter of the shop
	 * 
	 * @param string $pVal
	 * @return number
	 */
	public function setMagasFor($pVal) {
		if (is_string($pVal) && !empty($pVal)) {
			$this->magasFor = \Utils::protect($pVal);
		}
		return 1;
	}
	
	/**
	 * Getter of the account number
	 * 
	 * @return string
	 */
	public function noCompte() {
		if (isset($this->noCompte)) {
			return $this->noCompte;
		} else {
			return "";
		}
	}
	
	/**
	 * Setter of the account number
	 * 
	 * @param string $pVal
	 * @return number
	 */
	public function setNoCompte($pVal) {
		if (is_string($pVal) && !empty($pVal)) {
			$this->noCompte = \Utils::protect($pVal);
			return 1;
		}
		$this->setError(self::INVALID_NO_COMPTE);
		return 0;
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
	 * @return number
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
	 * Getter of the contact
	 * 
	 * @return \Library\Entities\Adresse|null
	 */
	public function contact() {
		if (isset($this->contact)) {
			return $this->contact;
		} else {
			return null;	
		}
	}
	
	/**
	 * Setter of the contact
	 * 
	 * @param \Library\Entities\Adresse $pVal
	 * @return number
	 */
	public function setContact(\Library\Entities\Adresse $pVal) {
			$this->contact = $pVal;
			return 1;
	}
	
	/**
	 * getter of the ParaGP address
	 * 
	 * @return \Library\Entities\Adresse|null
	 */
	public function factAdresse() {
		if (isset($this->factAdresse))
			return $this->factAdresse;
		else
			return null;
	}
	
	/**
	 * Setter of the ParaGP address
	 * 
	 * @param \Library\Entities\Adresse $pVal
	 * @return number
	 */
	public function setFactAdresse(\Library\Entities\Adresse $pVal) {
			$this->factAdresse = $pVal;
			return 1;
	}
	
	/**
	 * Getter of the client address
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
	 * Setter of the client address
	 * 
	 * @param \Library\Entities\Adresse $pVal
	 * @return number
	 */
	public function setAdresse(\Library\Entities\Adresse $pVal) {
			$this->adresse = $pVal;
			return 1;
	}
	
	/**
	 * Getter of the facture ID
	 * 
	 * @return number|number
	 */
	public function no() {
		if (isset($this->no)) {
			return $this->no;
		} else {
			return -1;
		}
	}
	
	/**
	 * setter of the facture ID
	 * 
	 * @param int $pVal
	 * @return number
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
	 * Getter of the client ID
	 * 
	 * @return number
	 */
	public function client_no() {
		if (isset($this->client_no))
			return $this->client_no;
		else
			return -1;
	}
	
	/**
	 * Setter of the client ID
	 * 
	 * @param unknown $pVal
	 * @return number
	 */
	public function setClient_no($pVal) {
		if (is_numeric($pVal) && !empty($pVal)) {
			$this->client_no = $pVal;
			return 1;
		}
		$this->setError(self::INVALID_CLIENT_NO);
		return 0;
	}
	
	/**
	 * Getter of the facturation date
	 * 
	 * @return \DateTime
	 */
	public function date_facturation() {
		
		if (isset($this->date_facturation)) {
			if (! ($this->date_facturation instanceof \DateTime)) {
				$this->date_facturation = new \DateTime($this->date_facturation);
			}
			return $this->date_facturation;
		} else {
			return new \DateTime();
		}
	}
	
	/**
	 * Setter of the facturation date
	 * 
	 * @param string|int[]|\DateTime $pVal
	 * @return number
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
	 * Getter of the TVA number
	 * 
	 * @return string
	 */
	public function noTva() {
		if (isset($this->noTva)) {
			return $this->noTva;
		} else {
			return "-1";
		}
	}
	
	/**
	 * Setter of the TVA number
	 * 
	 * @param string $pVal
	 * @return number
	 */
	public function setNoTva($pVal) {
		if (is_string($pVal) && !empty($pVal)) {
			$this->noTva = $pVal;
			return 1;
		}
		$this->setError(self::INVALID_NO_TVA);
		return 0;
	}
	
	/**
	 * Getter of the items
	 * 
	 * @return \Library\Facture\FactureElement[]
	 */
	public function designation() {
		if (isset($this->designation)) {
			return $this->designation;
		} else {
			return array();
		}
	}
	
	/**
	 * Setter of the designation
	 * 
	 * @param \Library\Facture\FactureElement $pVal
	 * @return number
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
	 * Adds a {@see \Library\Facture\FactureElement} at the end of the item list
	 * 
	 * @param \Library\FactureElement $pVal
	 * @return number
	 */
	public function addDesignation(\Library\FactureElement $pVal) {
		$test = false;
		
		foreach ($this->designation AS $key=>$elem) {
			if (!$test && $elem->equals($pVal)) {
				$elem->setNbr($pVal->nbr() + $elem->nbr());
				$test = true;
			}
		}
		
		if (!$test)
			$this->designation[]  = $pVal;
		
		return 1;
	}
	
	/**
	 * Getter of the time to pay
	 * 
	 * @return number
	 */
	public function time_to_pay() {
		if (isset($this->time_to_pay)) {
			return $this->time_to_pay;
		} else {
			return -1;
		}
	}
	
	/**
	 * Setter of the time to pay
	 * 
	 * @param int $pVal
	 * @return number
	 */
	public function setTime_to_pay($pVal) {
		if (is_numeric($pVal) && !empty($pVal)) {
			$this->time_to_pay = $pVal;
			return 1;
		}
		
		$this->setError(self::INVALID_TIME_TO_PAY);
		return 1;
	}
	
	/**
	 * Checks if there are errors
	 * 
	 * @return boolean
	 */
	public function isError() {
		return count($this->error) != 0;
	}
	
	/**
	 * Get all the different errors
	 * 
	 * @return string[]
	 */
	public function error() {
		return $this->error();
	}
	
	/**
	 * Adds a new error at the end of the error list
	 * 
	 * @param string $pVal
	 */
	public function setError($pVal) {
		if (!in_array($pVal, $this->error))
			$this->error[] = $pVal;
	}
	
	/**
	 * Returns the current facturation price. The price is calculated with the sum of every different items
	 * 
	 * @return number
	 */
	public function price() {
		$tPrice = 0;
		
		foreach ($this->designation AS $designation)
			$tPrice += $designation->price() * $designation->nbr();
		
		return $tPrice;
	}
	
	/**
	 * Gets the TVA price of the facturation. Calculate the TVA price which is 8% of the price
	 * 
	 * @return number
	 */
	public function getTvaPrice() {
		return $this->price() * 0.08;
	}
	
	/**
	 * Gets the full price. The full price is the sum of the current price and the TVA price
	 * 
	 * @return number
	 */
	public function getFullPrice() {
		return $this->price() * 1.08;
	}
	
	/**
	 * returns the number of centi of the price
	 * 
	 * @return int
	 */
	public function getCtPrice() {
		$temp = explode(".", \Utils::getMoneyFormat("CHF", $this->getFullPrice(), false));
		return $temp[1];
	}
	
	/**
	 * Returns the price without the cent
	 * 
	 * @return int
	 */
	public function getIntPrice() {
		$temp = explode(".", \Utils::getMoneyFormat("CHF", $this->getFullPrice()));
		return $temp[0];
	}
	
	/**
	 * Returns the number of the BVR Given the date of facturation and the ID of the facturation, calculate a valid BVR number
	 * 
	 * @return string
	 */
	public function getBvrNo() {
		$bvr = new Bvr();
		return $bvr->calculateCode($this->date_facturation(), $this->no);
		
	}
	
	/**
	 * Calculates the BVR cheksum.
	 * 
	 * Given the account number, the price, the currency and the BVR number, calculates the checksum for the BVR
	 * 
	 * @return string
	 */
	public function getCheckNo() {
		$bvr = new Bvr();
		
		return $bvr->calculate($this->noCompte, \Utils::getMoneyFormat("CHF", $this->getFullPrice(), false), "CHF", $this->getBvrNo());
	}
	
	/**
	 * Generates the facturation
	 * 
	 * Generates the facturation in a PDF way
	 * 
	 * @param string $firstInfo
	 * 		Information that will be added at the begin of the page
	 * @param string $lastInfo
	 * 		Information that will be added at the end of the page
	 * @param array $otherInfo
	 * 		Information in array that will be added just after firstInfo
	 * 
	 * @return string
	 */
	public function generate($firstInfo = '', $lastInfo = '', array $otherInfo = array()) {
		$formatLanguage = \Utils::getDateFormat(\Utils::getFormatLanguage($this->language()));
		
		$ret = '<page format="A4">';
		
		$ret .= $firstInfo;
		
		foreach ($otherInfo AS $info) {
			$ret .= $info;
		}
		
		$ret .= '<div style="position:absolute;top:200px;left:50px;width:200px; font-size: 10px;">';
			$ret .= '<div style="font-weight: bold;">' . COMMERCIAL_CONTACT . '</div>';
			$ret .= '<div>' . $this->contact() . '</div>';
		$ret .= '</div>';
		
		$ret .= '<div style="position:absolute;top:200px;left:450px;width:200px;font-size:13px;">';
			$ret .= '<div>' . $this->adresse() . '</div>';
		$ret .= '</div>';
		
		if (isset($this->no)) {
			$ret .= '<div style="position: absolute; top: 350px; left: 5%; font-size: 16px; font-weight: bold;">';
				$ret .= "Facture N° PLO2." . $this->no();
			$ret .= '</div>';
		}
		if ($this->magasFor() != "") {
			$ret .= '<div style="position: absolute; top: 370px; left: 5%; font-size: 10px;">';
				$ret .= "Magasin concerné: " . $this->magasFor();
			$ret .= '</div>';
		}
		
		$ret .= '<div style="position: absolute; top: 350px; left: 450px; font-size: 14px;">';
			$ret .= 'Neuchâtel, le ' . \Utils::formatDate($this->date_facturation(), $formatLanguage[1]);
		$ret .= '</div>';
		
		$ret .= '<div style="position: absolute; top: 385px; right: 40px; font-size: 8px;">';
			$ret .= 'N° TVA	: ' . $this->noTva();
		$ret .= '</div>';
		
		$ret .= '<div style="position:absolute;left:5%;width:90%;top:400px;">';
			$ret .= '<div style="width:100%">';
			$ret .= '<table style="width:100%;border:0px;border-top:1px solid black;border-bottom:1px solid black">';
				$ret .= '<tr>';
					$ret .= '<td style="width:60%">';
						$ret .= DESIGNATION;
					$ret .= '</td>';
					$ret .= '<td style="width:10%">';
						$ret .= NOMBRE;
					$ret .= '</td>';
					$ret .= '<td style="width:15%">';
						$ret .= PRIX_HT;
					$ret .= '</td>';
					$ret .= '<td style="width:15%">';
						$ret .= TOTAL;
					$ret .= '</td>';
				$ret .= '</tr>';
			$ret .= '</table>';
			$ret .= '</div>';
		$ret .= '</div>';
		$ret .= '<div  style="position:absolute;top:420px;">';
			$ret .= '<div>';
			foreach ($this->designation AS $key=>$desc) {
				if ($key % 2 == 1) {
					$ret .= '</div><div>';
				}
				$ret .= '<div style="position:relative;width:90%; left:5%; margin-top: 20px;">' . $desc->generate() . '</div>';
			}
			$ret .= '</div>';
		$ret .= '<div style="position:realtive; width: 100%; height: 150px; background-color: #ffffff"></div>';
		$ret .= '</div>';
		

		$ret .= '<div style="position:absolute;bottom:100px;left:5%; width: 90%; border: 1px solid #bbbbbb; height: 50px;">'
				. '<div style="position: absolute; top: 10px; left: 150px; width: 120px; font-size: 10px;">'
					. '<div style="position: absolute; left: 0px;">'
						. TOTAL 
					. '</div>'
					. '<div style="position: absolute; right: 0px;">'
						. \Utils::getMoneyFormat("CHF", $this->price())
					. '</div>'
				. '</div>'
				. '<div style="position: absolute; top: 10px; left: 300px; width: 120px; font-size: 10px;">'
					. '<div style="position: absolute; left: 0px;">'
						. 'TVA (8%)' 
					. '</div>'
					. '<div style="position: absolute; right: 0px;">'
						. \Utils::getMoneyFormat("CHF", ($this->price() * 0.08))
					. '</div>'
				. '</div>'
				. '<div style="position: absolute; font-size: 10px; left: 5%; bottom: 10px; width: 150px;">'
					. TIME_TO_PAY_1 . $this->time_to_pay() . TIME_TO_PAY_2
				. '</div>'
				. '<div style="font-weight: bold; position: absolute; font-size: 10px; right: 5%; bottom: 20px; width: 150px;">'
					. '<div style="position: absolute; left: 5px;">'
						. TOTAL_FACT
					. '</div>'
					. '<div style="position: absolute; right: 5px;">'
						. \Utils::getMoneyFormat("CHF", ($this->price() * 1.08))
					. '</div>'
				. '</div>';
		$ret .= '</div>';
		
		$ret .= $lastInfo;
		
		$ret .= '</page>';
		
		$ret .= '<page  format="A4">';
		$ret .= '<div style="position:absolute;top:200px;left:450px;width:200px;font-size:13px;">';
			
			$ret .= '<div>' . $this->adresse . '</div>';
			
		$ret .= '</div>';
			
		$ret .= '<div style="position: absolute; top: 600px; width: 90%; height: 100px; left: 5%; border: 1px solid #bbbbbb;">';
			$ret .= '<div style="position: absolute; top: -6px; left: 10px; background-color: #ffffff; width: 130px;text-align: center; font-size: 10px;">';
				$ret .= strtoupper(RAPPEL_FACT);
			$ret .= '</div>'
					. '<div style="position: absolute; top: 10px; left: 150px; width: 120px; font-size: 10px;">'
						. '<div style="position: absolute; left: 0px;">'
							. TOTAL 
						. '</div>'
						. '<div style="position: absolute; right: 0px;">'
							. \Utils::getMoneyFormat("CHF", $this->price())
						. '</div>'
					. '</div>'
					. '<div style="position: absolute; top: 10px; left: 300px; width: 120px; font-size: 10px;">'
						. '<div style="position: absolute; left: 0px;">'
							. 'TVA (8%)' 
						. '</div>'
						. '<div style="position: absolute; right: 0px;">'
							. \Utils::getMoneyFormat("CHF", ($this->price() * 0.08))
						. '</div>'
					. '</div>'
					. '<div style="position: absolute; font-size: 10px; left: 5%; top: 40px; width: 150px;">'
						. TIME_TO_PAY_1 . $this->time_to_pay() . TIME_TO_PAY_2
					. '</div>'
					. '<div style="font-weight: bold; position: absolute; font-size: 10px; right: 5%; top: 40px; width: 150px;">'
						. '<div style="position: absolute; left: 5px;">'
							. TOTAL_FACT
						. '</div>'
						. '<div style="position: absolute; right: 5px;">'
							. \Utils::getMoneyFormat("CHF", ($this->price() * 1.08))
						. '</div>'
					. '</div>'
					. '<div style="position: absolute; bottom: 10px; left: 5px; font-size: 10px;">'
						. THANKS
					. '</div>'
					. '<div style="position: absolute; bottom: 10px; width: 100%; text-align: center; font-size: 8px;">'
						. JOINED_BVR_ONLY
					. '</div>';
		$ret .= '</div>';
		
		$ret .= '<div style="position: absolute; bottom: 10px; height: 350px;">'
				. '<div style="width: 100px; position: absolute; top: 30px; left: 10px; height: 85px;">'
					. '<div style="position: absolute; top: 0px; left: 0px; font-weight: bold;">'
						. $this->factAdresse()
					. '</div>'
				. '</div>'
				. '<div style="width: 100px; position: absolute; top: 30px; left: 225px; height: 85px;">'
					. '<div style="position: absolute; top: 0px; left: 0px; font-weight: bold;">'
						. $this->factAdresse()
					. '</div>'
				. '</div>'
				
				. '<div style="position: absolute; top: 145px; left: 135px;">'
					. $this->noCompte()
				. '</div>'
				. '<div style="position: absolute; top: 145px; left: 375px;">'
					. $this->noCompte()
				. '</div>'
				
				. '<div style="position: absolute; top: 100px; left: 500px;">'
					. $this->getBvrNo()
				. '</div>'
				
				. '<div style="position: absolute; top: 170px; left: 155px; width: 40px;">'
					. '<div style="position: absolute; top: 0px; right: 45px;">'
						. $this->getIntPrice()
					. '</div>'
					. '<div style="position: absolute; top: 0px; right: 0px;">'
						. $this->getCtPrice()
					. '</div>'
				. '</div>'
				
				. '<div style="position: absolute; top: 170px; left: 395px; width: 40px;">'
					. '<div style="position: absolute; top: 0px; right: 45px;">'
						. $this->getIntPrice()
					. '</div>'
					. '<div style="position: absolute; top: 0px; right: 0px;">'
						. $this->getCtPrice()
					. '</div>'
				. '</div>'
						
				. '<div style="position: absolute; top: 200px; left: 5px; width: 300px;">'
					. '<div style="position: absolute; top: 3px;">'
						. $this->getBvrNo()
					. '</div>'
					. '<div style="position: absolute; top: 25px; left: 10px;">'
						. $this->adresse()
					. '</div>'
				. '</div>'
				
				. '<div style="position: absolute; top: 170px; left: 490px; width: 300px;">'
					. $this->adresse()  
				. '</div>'
						
				. '<div style="position: absolute; top: 290px; left: 390px; width: 400px;">'
					. $this->getCheckNo()
				. '</div>'
				
			. '</div>';
		
		return $ret . '</page>';
	}
}

?>