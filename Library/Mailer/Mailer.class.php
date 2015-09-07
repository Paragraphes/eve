<?php

namespace Library\Mailer;

if (!defined("EVE_APP"))
	exit();

/**
 * Class used to send mail.
 * 
 * This class gives general function to send an email using {@see \Library\Utils\PHPMailer\PHPMailer} given all the specific attributes
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 */
class Mailer extends \Library\ApplicationComponent {
	/**
	 * email of the sender
	 * @var string
	 */
	protected $sender;
	
	/**
	 * array of the different receivers of the email
	 * @var string[]
	 */
	protected $reciever = array();
	
	/**
	 * Email of the default sender
	 * @var string
	 */
	protected $defaultSender;
	
	/**
	 * The file in which the content of the URL is
	 * This file can contain som constant value to transforme them using
	 * the controler
	 * @var string
	 */
	protected $file;
	
	/**
	 * Text of the email.
	 * 
	 * Used if the file is not provided
	 * 
	 * @var string
	 */
	protected $text;
	
	/**
	 * All the different values and the different constants of the file.
	 * 
	 * The key of the array are the different constants and the elements are the different values of this constants for this email.
	 *  
	 * @var string
	 */
	protected $fileValue = array();
	
	/**
	 * subject of the email
	 *  
	 * @var string
	 */
	protected $subject;
	
	/**
	 * Get the defaultSender.
	 * If no default sender is provided, then the default user in configuration is provided
	 * @return string
	 */
	public function defaultSender() {
		if (!isset($defaultSender))
			$this->defaultSender = $this->app->config()->get("DEFAULT_MAIL_SENDER");
		
		return $this->defaultSender;
	}
	
	/**
	 * Setter of the sender
	 * 
	 * @param string $pVal
	 * @return number
	 */
	public function setSender($pVal) {
		if (\Utils::testEmail($pVal)) {
			$this->sender = $pVal;
			return 1;
		}
		return 0;
	}
	
	/**
	 * Returns the current sender or the default sender if no sender is provided
	 * 
	 * @return string
	 */
	public function sender() {
		if (isset($this->sender)) {
			return $this->sender;
		} else {
			return $this->defaultSender();
		}
	}
	
	/**
	 * Adds a receiver on the list
	 * 
	 * @param string $pVal
	 * @return number
	 */
	public function addReciever($pVal) {
		if (\Utils::testEmail($pVal) && !in_array($pVal, $this->reciever)) {
			$this->reciever[] = $pVal;
			return 1;
		}
		return 0;
	}
	
	/**
	 * Sets one or more receiver on the list and removes all other
	 * 
	 * @param string|string[] $pVal
	 * @return number
	 */
	public function setReciever($pVal) {
		$this->reciever = array();
		if (is_array($pVal)) 
			foreach ($pVal AS $val)
				$this->addReciever($val);
		else
			$this->addReciever($pVal);
	}
	
	/**
	 * Checks if a receiver exists on the list and removes it
	 * 
	 * @param string $pVal
	 * @return number
	 */
	public function removeReciever($pVal) {
		if(($key = array_search($del_val, $messages)) !== false) {
		    unset($messages[$key]);
		    return 1;
		}
		return 0;
	}
	
	/**
	 * Removes all receivers
	 * 
	 * @return number
	 */
	public function initReciever() {
		$this->reciever = array();
		return 1;
	}
	
	/**
	 * Setter of the text
	 * 
	 * @param string $pVal
	 * @return number
	 */
	public function setText($pVal) {
		if (is_string($pVal) && !empty($pVal)) {
			$this->text = ($pVal);
		}
		return 1;
	}
	
	/**
	 * Setter of the file
	 * 
	 * @param string $pVal
	 * @return number
	 */
	public function setFile($pVal) {
		if (file_exists($pVal)) {
			$this->file = $pVal;
			return 1;
		}
		return 0;
	}
	
	/**
	 * Sets an array of file values
	 * 
	 * @param string[] $pVal
	 * @return number
	 */
	public function setFileValue(array $pVal) {
		$ret = 1;
		foreach ($pVal AS $key=>$value) {
			$ret = $ret * $this->addFileValue($key, $value);
		}
		return $ret;
	}
	
	/**
	 * Adds a file value
	 * @param string $pKey
	 * 		Const on the file
	 * @param string $pVal
	 * 		Value of the constant
	 * @return number
	 */
	public function addFileValue($pKey, $pVal) {
		if (!array_key_exists($pKey, $this->fileValue)) {
			$this->fileValue[$pKey] = $pVal;
			return 1;
		}
		return 0;
	}
	
	/**
	 * Removes a file value that has been given a key
	 * 
	 * @param string $pKey
	 * @return number
	 */
	public function removeFileValue($pKey) {
		if (!array_key_exists($pKey, $this->fileValue)) {
			unset($this->fileValue[$pKey]);
			return 1;
		}
		return 0;
	}
	
	/**
	 * Removes all the file value constants
	 */
	public function initFileValue() {
		$this->fileValue = array();
	}
	
	/**
	 * adds a sbject to the mail
	 * 
	 * @param string $pVal
	 */
	public function setSubject($pVal) {
		if (!empty($pVal)) {
			$this->subject = $pVal;
		}
	}
	
	/**
	 * Removes all the different informations of the mail
	 */
	public function init() {
		unset($this->sender);
		$this->reciever = array();
		unset($this->file);
		$this->fileValue = array();
		unset($this->subject);
		unset($this->text);
	}
	
	/**
	 * Returns the text of the email
	 * 
	 * - If there is a file provided, gets the content of the file
	 * - If there is a text, provides this text
	 * 
	 * And then replace all the different value constants by their values
	 * 
	 * The file is given priority
	 * 
	 * @return mixed
	 */
	public function getText () {
		if (isset ($this->file))
			$txtMail = file_get_contents($this->file);
		else
			$txtMail = $this->text;
		
		foreach($this->fileValue AS $key => $value)
			$txtMail = str_replace($key, $value, $txtMail);
		
		return $txtMail;
	}
	
	/**
	 * Checks if a mailer is valid. It means that
	 * 
	 * - A file or a text is provided
	 * - A subject is provided
	 * - At least a receiver is provided
	 * 
	 * @return boolean
	 */
	public function isValid() {
		return (isset($this->file) || isset($this->text)) && isset($this->subject) && (count($this->reciever) != 0);
	}
	
	/**
	 * Method that sends the mail
	 * 
	 * @throws \RuntimeException
	 * 			if the mail is not valid
	 * 
	 * @return boolean
	 */
	public function sendMail(){
		
		if (!$this->isValid()) {
			ob_start();
			echo "<p>Error on Mailer</p>";
			echo "<p>file</p>";
			var_dump($this->file);
			echo "<p>text</p>";
			var_dump($this->text);
			echo "<p>subject</p>";
			var_dump($this->subject);
			echo "<p>reciever</p>";
			var_dump($this->reciever);
			$ret = ob_get_clean();
			
			error_log($ret);
			
			if (\Library\Application::appConfig()->getConst("LOG"))
				throw new \RuntimeException("Error ID: " . \Library\Application::logger()->log("Error", "Email", "Miss element to send an email" . $ret, __FILE__, __LINE__));
			else
				throw new \RuntimeException("Miss element to send an email");
		}
		
		$phpMail = new \Library\Utils\PHPMailer\PHPMailer();
		
		$phpMail->IsSMTP();
		$phpMail->Port = $this->app->config()->get("SMTP_PORT");
		$phpMail->Host = $this->app->config()->get("SMTP_SERVER");
		 
		$phpMail->Mailer = "smtp";
		$phpMail->SMTPSecure = "ssl";
		
		$phpMail->SMTPAuth = true;
		$phpMail->Username = $this->app->config()->get("SMTP_LOGIN");
		$phpMail->Password = $this->app->config()->get("SMTP_PASS");
		
		$phpMail->SMTPDebug = 0;
		
		$phpMail->From = $this->sender();
		$phpMail->FromName = "Paraprinting";

		$phpMail->AddReplyTo($this->sender(), "Paraprinting");
		
		$phpMail->CharSet = 'UTF-8';
		foreach ($this->reciever AS $reciever) {
			$phpMail->AddAddress($reciever);
		}
		
		$phpMail->Subject= $this->subject;
		
		$phpMail->Body = $this->getText();
		
		$phpMail->IsHTML(true);
		
		
		if($phpMail->Send()){
			return true;
		}else{
			return false;
		}
		
		return true;
	}
}

?>