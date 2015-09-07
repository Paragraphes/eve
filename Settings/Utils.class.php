<?php

if (!defined("EVE_APP"))
	exit();

/**
 * Different utils method that could be used by
 * all the different classes of the application
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class Utils{
	
	/**
	 * Returns the value sent by the parameters protected and converted in UTF-8
	 * 
	 * All the quotes are backslashed
	 * 
	 * @param string $pVal
	 * @return string
	 * @static
	 */
	public static function protect($pVal){
		return htmlentities($pVal, ENT_QUOTES, "UTF-8");
	}
	
	/**
	 * Encrypts a message with a specific salt.
	 * 
	 * The best solution is to call {@see \Utils::getBlowfishSalt} for the first call, then to use the hashed password as salt to compare.
	 * 
	 * @param string $pPass
	 * @param string $pSalt
	 * @return string
	 * @static
	 */
	public static function hash($pPass, $pSalt){
		return crypt($pPass, $pSalt);
	}
	
	/**
	 * Returns the DPI of an image
	 * 
	 * @param String $filename
	 * @return number[]
	 * @static
	 */
	public static function get_dpi($filename){
		$a = fopen($filename,'r');
		$string = fread($a,20);
		fclose($a);
	
		$data = bin2hex(substr($string,14,4));
		$x = substr($data,0,4);
		$y = substr($data,4,4);
	
		return array(hexdec($x),hexdec($y));
	}
	
	/**
	 * Returns a Blowfish salt
	 * 
	 * 
	 * @param number $rounds
	 * @return string
	 * @static
	 */
	public static function getBlowfishSalt($rounds = 7){
		$salt = "";

		$salt_chars = array_merge(range('A','Z'), range('a','z'), range(0,9));

		for($i=0; $i < 22; $i++){
			$salt .= $salt_chars[array_rand($salt_chars)];
		}

		return sprintf('$2a$%02d$', $rounds) . $salt;
	}
	
	/**
	 * Checks if the parameter is a well formated email
	 * 
	 * @param string $pVal
	 * @return boolean
	 * @static
	 */
	public static function testEmail($pVal){
		if(filter_var($pVal, FILTER_VALIDATE_EMAIL) !== false){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Checks if the parameter is a well formated URL
	 * 
	 * @param unknown $pVal
	 * @return boolean
	 * @static
	 */
	public static function testUrl($pVal){
		echo $pVal;
		if(filter_var($pVal, FILTER_VALIDATE_URL) !== false){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Returns the default language of the application
	 * 
	 * @return string
	 * @deprecated
	 * @static
	 */
	public static function defaultLanguage(){
		return 'fr-FR';
	}
	
	/**
	 * Returns a date formated in a SQL format
	 * 
	 * @param \DateTime $pDate
	 * @return String
	 */
	public static function dateToDb(\DateTime $pDate){
		return $pDate->format("Y-m-d H:i:s");//2014-08-29 16:19:42
	}
	
	/**
	 * Returns a date formated for a region.
	 * 
	 * Given a specific (well formated) language, retieve the date format of the language as an array
	 * 
	 * - 0 => Asked format
	 * - 1 => standard format for {@see \DateTime}
	 * - 2 => Regular expression to check if the format is right
	 * - 3 => standard format for {@see \DateTime}
	 * - 4 => standard representation for {@see \DataTime}
	 * 
	 * Returns the date format according to the user's language as an array(asked format, standard format for {@see \DateTime}, regular expression)
	 * 
	 * @param String $pVal
	 * @return string[]
	 */
	public static function getDateFormat($pVal){
		
		$val = array(
				'fr-FR' => array('dd/mm/yyyy', 'd-m-Y', '^(([0-9]{1}|[0-9]{2})-([0-9]{1}|[0-9]{2})-(((19)|(20)){1}[0-9]{2}))$', 'd-m-Y G:i:s', '%a j %h h'),
				'de-DE' => array('dd/mm/yyyy', 'd-m-Y', '^(([0-9]{1}|[0-9]{2})-([0-9]{1}|[0-9]{2})-(((19)|(20)){1}[0-9]{2}))$', 'd-m-Y G:i:s', '%a Tg %h St')
				);

		if (!key_exists($pVal, $val))
			$pVal = self::defaultLanguage();
		
		return $val[$pVal];
	}
	
	/**
	 * Returns the different valid language.
	 * The key is the formated language and the valueis a constant representing a language.
	 * 
	 * @return string[]
	 */
	public static function getListLanguage(){
		return array(
				'fr-FR'=>'Français',
				'de-DE'=>'Allemand'
		);
	}
	
	/**
	 * Returns a value formated like a specific money.
	 * 
	 * @param string $currency
	 * @param int $value
	 * @return string
	 */
	public static function getMoneyFormat($currency, $value, $showCurrency = true) {
		switch ($currency) {
			case 'CHF':
			default:
				if ($showCurrency) {
					return number_format((round($value * 0.2)/20), 2, ".", "") . ' CHF';
				} else {
					return number_format((round($value * 0.2)/20), 2, ".", "");
				}
		}
	}
	
	/**
	 * Returns a date formated on a specific format.
	 * 
	 * @param \DateTime $date
	 * @param string $format
	 * @return string
	 */
	public static function formatDate($date, $format){
		
		if($date instanceof \DateTime && !empty($format)){
			return $date->format($format);
		}else{
			return '-';
		}
	}
	
	/**
	 * Returns the standard format of a language given a general language case.
	 * 
	 * @param string $lang
	 * @return string
	 */
	public static function getFormatLanguage($lang){
		switch(strtolower($lang)){
			case "fr-ch":
			case "fr-lu":
			case "fr-ca":
			case "fr-be":
			case "fr-fr":
			case "fr":
			case "fr-FR":
				return 'fr-FR';
				break;
			case "de":
			case "de-at":
			case "de-li":
			case "de-lu":
			case "de-ch":
			case "de-de":
				return 'de-DE';
				break;/*
			case "it-ch":
			case "it":
				return 'it-IT';
				break;
			case "en-us":
			case "en-za":
			case "en-bz":
			case "en-gb":
			case "en":
				return 'en-EN';
				break;//*/
		default:
			return Utils::defaultLanguage();
		}
	}
}

?>