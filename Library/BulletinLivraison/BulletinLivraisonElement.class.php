<?php

namespace Library\BulletinLivraison;

if (!defined("EVE_APP"))
	exit();

/**
 * A class that generates the items of a {@see \Library\BulletinLivraison\BulletinLivraison}. This class is recursive to have items that have some kind of items.
 * 
 * @see \Library\BulletinLivraison\BulletinLivraison
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class BulletinLivraisonElement {
	
	/**
	 * Name of the item
	 * @var string
	 */
	protected $name;
	
	/**
	 * Element that describes the element
	 * @var string[]
	 */
	protected $desc = array();
	
	/**
	 * How many items there is in the bulletin
	 * @var int
	 */
	protected $nbr;
	
	/**
	 * List of sub element
	 * @var \Library\BulletinLivraison\BulletinLivraisonElement[]
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
	 * getter of the description
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
	 * Setter of the description
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
	 * Add a specific description on at the end of the description list
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
	 * Getter of the number of items
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
	 * Setter of the number ot items
	 * 
	 * @param int $pVal
	 * @return int
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
	 * Getter of the sub element list
	 * 
	 * @return \Library\BulletinLivraison\BulletinLivraisonElement[]
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
	 * @param \Library\BulletinLivraison\BulletinLivraisonElement|\Library\BulletinLivraison\BulletinLivraisonElement[] $pVal
	 * @return int
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
	 * Adds a specific {@see \Library\BulletinLivraison\BulletinLivraisonElement} at the end of the sub item list.
	 * 
	 * @param \Library\BulletinLivraison\BulletinLivraisonElement $pVal
	 * @return int
	 */
	public function addListeElem(\Library\BulletinLivraison\BulletinLivraisonElement $pVal) {
		if (!empty($pVal) && !in_array($pVal, $this->listeElem)) 
			$this->listeElem[] = $pVal;
		
		return 1;
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
					if ($this->nbr != -1) {
						$ret .= '<tr>'
									. '<td style="width:70%; font-weight: bold;">'
										. '-' . strtoupper($this->name()) . '-'
									. '</td>'
									. '<td style="width:30%; text-align: right;">'
										. $this->nbr()
									. '</td>'
								. '</tr>';
					} else {
						$ret .= '<tr>'
								. '<td>'
									. '' . strtoupper($this->name()) . ''
								. '</td>'
								. '<td>'
								. '</td>'
							. '</tr>';
					}
		
		foreach ($this->desc AS $desc) {
			$ret .= '<tr>'
					. '<td>'
						. $desc
					. '</td>'
					. '<td>'
					. '</td>'
				. '</tr>';
		}
		
		foreach ($this->listeElem AS $elem) {
			
			$ret .= '<tr>'
					. '<td colspan="2">'
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