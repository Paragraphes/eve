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
 * @author Toudoudou
 * @version 1.0
 */
class Mailer extends \Library\ApplicationComponent {
	
	const ERROR1300 = "Error 1300: Sender [%s] could not be added.";
	const ERROR1301 = "Error 1301: Receiver [%s] could not be added.";
	const ERROR1305 = "Error 1305: Text is invalid.";
	const ERROR1310 = "Error 1310: Could not find receiver [%s].";
	const ERROR1320 = "Error 1320: The file [%s] does not exist.";
	const ERROR1330 = "Error 1330: Could not add file value.";
	const ERROR1331 = "Error 1331: Could not add file value.";
	const ERROR1340 = "Error 1340: Missing element to send an email.";
	const ERROR1345 = "Error 1345: Email failed to send.";
	
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
	 */
	public function setSender($pVal) {
		if (\Utils::testEmail($pVal)) {
			$this->sender = $pVal;
		} else {
			throw new \Library\Exception\MailerException(\Library\Application::logger()->log("Error", "Mailer", sprintf(self::ERROR1300, $pVal), __FILE__, __LINE__));
		}
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
	 */
	public function addReceiver($pVal) {
		if (\Utils::testEmail($pVal) && !in_array($pVal, $this->reciever)) {
			$this->reciever[] = $pVal;
		} else {
			throw new \Library\Exception\MailerException(\Library\Application::logger()->log("Error", "Mailer", sprintf(self::ERROR1301, $pVal), __FILE__, __LINE__));
		}
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
				$this->addReceiver($val);
		else
			$this->addReceiver($pVal);
	}
	
	/**
	 * Checks if a receiver exists on the list and removes it
	 * 
	 * @param string $pVal
	 */
	public function removeReciever($pVal) {
		if(($key = array_search($del_val, $messages)) !== false) {
		    unset($messages[$key]);
		} else {
			throw new \Library\Exception\MailerException(\Library\Application::logger()->log("Error", "Mailer", sprintf(self::ERROR1310, $pVal), __FILE__, __LINE__));
		}
	}
	
	/**
	 * Removes all receivers
	 */
	public function initReciever() {
		$this->reciever = array();
	}
	
	/**
	 * Setter of the text
	 * 
	 * @param string $pVal
	 */
	public function setText($pVal) {
		if (is_string($pVal) && !empty($pVal)) {
			$this->text = ($pVal);
		} else {
			throw new \Library\Exception\MailerException(\Library\Application::logger()->log("Error", "Mailer", self::ERROR1305, __FILE__, __LINE__));
		}
	}
	
	/**
	 * Setter of the file
	 * 
	 * @param string $pVal
	 */
	public function setFile($pVal) {
		if (file_exists($pVal)) {
			$this->file = $pVal;
		} else {
			throw new \Library\Exception\MailerException(\Library\Application::logger()->log("Error", "Mailer", sprintf(self::ERROR1320, $pVal), __FILE__, __LINE__));
		}
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
	 */
	public function addFileValue($pKey, $pVal) {
		if (!array_key_exists($pKey, $this->fileValue)) {
			$this->fileValue[$pKey] = $pVal;
		} else {
			throw new \Library\Exception\MailerException(\Library\Application::logger()->log("Error", "Mailer", self::ERROR1330, __FILE__, __LINE__));
		}
	}
	
	/**
	 * Removes a file value that has been given a key
	 * 
	 * @param string $pKey
	 */
	public function removeFileValue($pKey) {
		if (!array_key_exists($pKey, $this->fileValue)) {
			unset($this->fileValue[$pKey]);
		} else {
			throw new \Library\Exception\MailerException(\Library\Application::logger()->log("Error", "Mailer", self::ERROR1331, __FILE__, __LINE__));
		}
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
			
			throw new \RuntimeException(\Library\Application::logger()->log("Error", "Mailer", self::ERROR1340 . $ret, __FILE__, __LINE__));
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
		
		
		if(!$phpMail->Send()){
			throw new \RuntimeException(\Library\Application::logger()->log("Error", "Mailer", self::ERROR1345, __FILE__, __LINE__));
		}
	}
}

?>