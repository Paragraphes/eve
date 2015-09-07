<?php

namespace Library\Facture;

if (!defined("EVE_APP"))
	exit();

/**
 * A class that generates the items of a {@see \Library\Facture\FactureElement} This class is recursive to have items that have some kind of items.
 * 
 * @see \Library\Facture\Facture
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class FactureElement {
	/**
	 * Name of the element
	 * @var string
	 */
	protected $name;
	
	/**
	 * Element that describes the element
	 * @var string[]
	 */
	protected $desc = array();
	
	/**
	 * How many items there is in the element
	 * @var int
	 */
	protected $nbr;
	
	/**
	 * item price in ct
	 * @var int
	 */
	protected $price;
	
	/**
	 * Sub element list
	 * @var \Library\Facture\Facture[]
	 */
	protected $listeElem = array();
	
	CONST INVALID_NAME = 0;
	CONST INVALID_DESC = 1;
	CONST INVALID_NBR = 2;
	CONST INVALID_PRICE = 3;
	CONST INVALID_LISTEELEM = 4;
	
	/**
	 * Getter of the error list
	 * @var string[]
	 */
	protected $error = array();
	
	/**
	 * Getter of the name
	 * 
	 * @return string
	 */
	public function name() {
		if (isset($this->name)) {
			if (defined($this->name)) {
				return constant($this->name);
			} else {
				return $this->name;
			}
		} else {
			return "";
		}
	}
	/**
	 * Checks the equality between two different {@see \Library\Facture\FactureElement}
	 * 
	 * Being the same means that their name and price are the same plus that the desc is the same for each of the different sub items the items are the same
	 * 
	 * @param \Library\Facture\FactureElement $factElem
	 * @return boolean
	 */
	public function equals(\Library\FactureElement $factElem) {
		$test = $this->name == $factElem->name && $this->price == $factElem->price;
		if ($test == false) {
			return false;
		}
		
		$listeDesc = $factElem->desc();
		
		foreach ($this->desc AS $key=>$elem) {
			if (key_exists($key, $listeDesc)) {
				$test = $test && ($elem == $listeDesc[$key]);
			} else {
				return false;
			}
			
			if ($test == false) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Setter of the name
	 * 
	 * @param string $pVal
	 * @return int
	 */
	public function setName($pVal) {
		if (is_string($pVal) && !empty($pVal)) {
			$this->name = ($pVal);
			return 1;
		}
		
		$this->setError(self::INVALID_NAME);
		return 0;
	}
	
	/**
	 * Getter of the description
	 * 
	 * @return string[]
	 */
	public function desc () {
		if (isset($this->desc)) {
			return $this->desc;
		} else {
			return array();
		}
	}
	
	/**
	 * setter of the description
	 * 
	 * @param string|string[] $pVal
	 * @return int
	 */
	public function setDesc($pVal) {
		if (is_array($pVal)) {
			$t = 1;
			foreach($pVal AS $val){
				$t *= $this->addDesc($val);
			}
		} else {
			$t = $this->addDesc($pVal);
		}
		
		return $t;
	}
	
	/**
	 * Adds a specific description at the end of the description list
	 * 
	 * @param string $pVal
	 * @return int
	 */
	public function addDesc($pVal) {
		if (!empty($pVal) && is_string($pVal) && !in_array($pVal, $this->desc)) {
			if (defined($pVal)) {
				$this->desc[] = constant($pVal);
			} else {
				$this->desc[] = ($pVal);
			}
			return 1;
		}
		
		$this->setError(self::INVALID_DESC);
		return 0;
	}
	
	/**
	 * Getter of the item number
	 * 
	 * @return int
	 */
	public function nbr() {
		if (isset($this->nbr)) {
			return $this->nbr;
		} else {
			return -1;
		}
	}
	
	/**
	 * Setter of the item number
	 * 
	 * @param unknown $pVal
	 * @return number
	 */
	public function setNbr($pVal) {
		if (is_numeric($pVal) && !empty($pVal)) {
			$this->nbr = $pVal;
			return 1;
		}
		$this->setError(self::INVALID_NBR);
		return 0;
	}
	
	/**
	 * Getter of the price
	 * 
	 * @return int
	 */
	public function price() {
		if (isset($this->price)) {
			return $this->price;
		} else {
			return -1;
		}
	}
	
	/**
	 * Setter of the price
	 * 
	 * @param unknown $pVal
	 * @return number
	 */
	public function setPrice($pVal) {
		if ( is_numeric($pVal) && !empty($pVal)) {
			$this->price = $pVal;
			return 1;
		}
		
		$this->setError(self::INVALID_PRICE);
		return 0;
	}
	
	/**
	 * Getter of the sub element list
	 * 
	 * @return \Library\Facture\FactureElement[]
	 */
	public function listeElem () {
		if (isset($this->listeElem)) {
			return $this->listeElem;
		} else {
			return array();
		}
	}
	
	/**
	 * Setter of the listElem
	 * 
	 * @param \Library\Facture\FactureElement|\Library\Facture\FactureElement[] $pVal
	 * @return number
	 */
	public function setListeElem($pVal) {
		if (is_array($pVal)) {
			$t = 1;
			foreach($pVal AS $val){
				$t *= $this->addListeElem($pVal);
			}
		} else {
			$t = $this->addListeElem($pVal);
		}
	
		return $t;
	}
	
	/**
	 * adds a specific {@see \Library\Facture\FactureElement} at the end of the sub item list
	 * 
	 * @param \Library\Facture\FactureElement $pVal
	 * @return number
	 */
	public function addListeElem($pVal) {
		if (!empty($pVal) && ($pVal instanceof \Library\FactureElement) && !in_array($pVal, $this->listeElem)) {
			$this->listeElem[] = $pVal;
			return 1;
		}
		
		$this->setError(self::INVALID_LISTEELEM);
		return 0;
	}
	
	/**
	 * Checks whether there is an error or not
	 * 
	 * @return boolean
	 */
	public function isError() {
		return count($this->error) != 0;
	}
	
	/**
	 * Getter of the error list
	 * 
	 * @return string[]
	 */
	public function error() {
		return $this->error();
	}
	
	/**
	 * Adds an error at the end of the error list
	 * 
	 * @param string $pVal
	 */
	public function setError($pVal) {
		if (!in_array($pVal, $this->error)) {
			$this->error[] = $pVal;
		}
	}
	
	/**
	 * Generates a well formated element that should be added in the PDF
	 * 
	 * The function works recursively on the sub items
	 * 
	 * @return string
	 */
	public function generate() {
		$ret =  '<table style="width:100%;" border="0">';
					if ($this->price() != -1 && $this->nbr != -1) {
						$ret .= '<tr>'
									. '<td style="width:60%; font-weight: bold;">'
										. '-' . strtoupper($this->name()) . '-'
									. '</td>'
									. '<td style="width:10%;">'
										. $this->nbr()
									. '</td>'
									. '<td style="width:15%;">'
										. \Utils::getMoneyFormat("CHF", $this->price)
									. '</td>'
									. '<td style="width:15%;">'
										. \Utils::getMoneyFormat("CHF", ($this->price * $this->nbr()))
									. '</td>'
								. '</tr>';
					} else {
						$ret .= '<tr>'
								. '<td>'
									. '>' . strtoupper($this->name()) . ''
								. '</td>'
								. '<td colspan="3">'
								. '</td>'
							. '</tr>';
					}
		
		foreach ($this->desc AS $desc) {
			$ret .= '<tr>'
					. '<td>'
						. $desc
					. '</td>'
					. '<td colspan="3">'
					. '</td>'
				. '</tr>';
		}
		
		foreach ($this->listeElem AS $elem) {
			
			$ret .= '<tr>'
					. '<td colspan="4">'
						. '<table style="width:100%">'
							. '<tr>'
								. '<td style="width:3%">'
								. '</td>'
								. '<td>'
									. $elem->generate()
								. '</td>'
							. '</tr>'
						. '</table>'
					. '</td>'
				. '</tr>';
		}
		
		$ret .= '</table>';
		
		return $ret;
	}
}

?>