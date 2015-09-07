<?php

namespace Library\Facture;

if (!defined("EVE_APP"))
	exit();

/**
 * Several payment system class
 *
 * @author DCU
 * @version $Id$
 * @copyright Developers & Consultants Union Ltd 2009
 * @package GBM
 */
 
$moduloTable = array(  array(0,9,4,6,8,2,7,1,3,5),
            array(9,4,6,8,2,7,1,3,5,0),
            array(4,6,8,2,7,1,3,5,0,9),
            array(6,8,2,7,1,3,5,0,9,4),
            array(8,2,7,1,3,5,0,9,4,6),
            array(2,7,1,3,5,0,9,4,6,8),
            array(7,1,3,5,0,9,4,6,8,2),
            array(1,3,5,0,9,4,6,8,2,7),
            array(3,5,0,9,4,6,8,2,7,1),
            array(5,0,9,4,6,8,2,7,1,3));
$moduloTableKey =    array(0,9,8,7,6,5,4,3,2,1);             
 
/**
 * This class generates BVR code for Swiss Post
 * Génération de code de bulletin de versement pour la poste Suisse
 * 
 * Note: this generates only BVR type BVR and not BVR+. For more info go to www.postfinance.ch
 * 
 */
class Bvr {
 
  var  $currency;
  var  $amount;
  var  $errorMessage;
  var  $FORMAT_AMOUNT = "/^([0-9]{1,8})\.?([0-9]{0,2})$/";
  var  $FORMAT_REFNUM = "/^[0-9]{1,26}$/";
  var  $FORMAT_ACCOUNT= "/^([0-9]{1,2})-([0-9]{1,6})-([0-9])$/";
 
  var  $currencyCode;
  var  $amountVal;
  var  $amountValCent;
  var  $refNumberFormated;
  var  $bvrNumber;
  var  $accountFormated;
 
  /**
   * Main function that calculate the BVR number. 
   * 
   * @param string   $account  Account number must be given with dash for example 01-1234-8
   * @param float    $amount    Only positve number or Zero xxx.xx
   * @param string  $currency  CHF or EUR
   * @param string  $refNumber  Number only string
   * @return string   Result of the BVR number. 
   */
  function calculate($account,$amount,$currency,$refNumber){
 
    if(  $this->checkCurrencyCode($currency)    ==-1 ||
      $this->checkAmountFormat($amount)    ==-1 ||
      $this->checkAccountFormat($account)    ==-1 ||
      $this->checkRefNumber($refNumber)    ==-1){
      $this->errorMessage .= "Error BVR number cannot be calculated. \n";
      return -1;
    }
 
    $amountFull =   str_pad($this->amountVal,8,"0",STR_PAD_LEFT).
            str_pad($this->amountValCent,2,"0",STR_PAD_RIGHT);
 
    $this->bvrNumber =   $this->currencyCode.
              $amountFull.
              moduloCoding($this->currencyCode.$amountFull).
              ">".
              $this->refNumberFormated.
              moduloCoding($this->refNumberFormated).
              "+ ".
              $this->accountFormated.
              moduloCoding($this->accountFormated).
              ">";
 
    return $this->bvrNumber;
  }
 
  /**
   * bvr::checkCurrencyCode()
   * 
   * @param string $currency
   * @return int Return -1 if error
   */
  function checkCurrencyCode($currency){
    $this->currency = strtolower(($currency)); 
    switch($this->currency){
      case "chf":
        $this->currencyCode = "01";
      break;
      case "eur":
        $this->currencyCode = "21";
      break;
      default:
        $this->errorMessage .= "Currency type unknown. \n";
        return -1;
    }
  }
 
  /**
   * In our case refernce number will have by default 27 positions 
   * 
   * @param string $refNumber
   * @return int Return -1 if error
   */
  function checkRefNumber($refNumber){
    $refNumber=str_replace(" ","",$refNumber);
    if (preg_match($this->FORMAT_REFNUM,$refNumber)){
      $this->refNumberFormated = str_pad($refNumber,26,"0",STR_PAD_LEFT);
    }else{
      $this->errorMessage .= "Reference number format error use only number, Maximum 26 positions  \n";
      return -1;
    }
  }
 
  /**
   * bvr::checkAmountFormat()
   * 
   * @param mixed $amount
   * @return int Return -1 if error
   */
  function checkAmountFormat($amount){
    // Check amount
    if (preg_match($this->FORMAT_AMOUNT,trim($amount),$amountSplited)){
      $this->amountVal = (int)$amountSplited[1];
      $this->amountValCent = str_pad((int)$amountSplited[2],2,"0",STR_PAD_RIGHT);
      $this->amount = $this->amountVal.".".$this->amountValCent;
    }else{
      $this->errorMessage .= "Amount number format error use xxxxxxxx.xx format \n";
      return -1;
    }
  }
 
  function calculateCode(\DateTime $date, $factureNo) {
	$code = '21 00000 ';
	$code .= $date->format("my");
	$code .= '0 00000 ';
	for($i = 0; $i<9; $i++){
		if(9-$i-strlen($factureNo) > 0){
			$code .= '0';
		}else{
			$code .= substr($factureNo, $i-9+strlen($factureNo), 1);
		}
		if($i == 4){
			$code .= ' ';
		}
	}
	
	return $code;
  }
  
  /**
   * Calculate the acount number string
   * Account number must be given with dash for example 01-1234-8
   * 
   * @param string $amount
   * @return int Return -1 if error
   */
  function checkAccountFormat($account){
    if (preg_match($this->FORMAT_ACCOUNT,trim($account),$accountSplited)){
      $this->accountFormated =   str_pad((int)$accountSplited[1],2,"0",STR_PAD_LEFT).
                    str_pad((int)$accountSplited[2],6,"0",STR_PAD_LEFT);
      // additional check
      
      if ((int)$accountSplited[3]<>moduloCoding($this->accountFormated)){
        $this->errorMessage .= "Accunt number error. \n";
        return -1;
      }
    }else{
      $this->errorMessage .= "Account format number error. \n";
      return -1;
    }
  }
 
}
 
 
/**
 * moduloCoding()
 * 
 * @param mixed $str
 * @return
 */
function moduloCoding($str){
 
	$moduloTable = array(  array(0,9,4,6,8,2,7,1,3,5),
	            array(9,4,6,8,2,7,1,3,5,0),
	            array(4,6,8,2,7,1,3,5,0,9),
	            array(6,8,2,7,1,3,5,0,9,4),
	            array(8,2,7,1,3,5,0,9,4,6),
	            array(2,7,1,3,5,0,9,4,6,8),
	            array(7,1,3,5,0,9,4,6,8,2),
	            array(1,3,5,0,9,4,6,8,2,7),
	            array(3,5,0,9,4,6,8,2,7,1),
	            array(5,0,9,4,6,8,2,7,1,3));
	$moduloTableKey =    array(0,9,8,7,6,5,4,3,2,1);
	
  $tmp = 0;
  for($i=0;  $i<strlen($str);  $i++){
    $tmp = $moduloTable[$tmp][$str[$i]];
  }
  
  return $moduloTableKey[$tmp];
}
 
?>