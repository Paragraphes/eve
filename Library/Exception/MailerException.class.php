<?php

namespace Library\Exception;

if (!defined("EVE_APP"))
	exit();

/**
 * A class that checks all PDO-related exceptions.
 *
 * @copyright ParaGP Swizerland
 * @author Toudoudou
 * @version 1.0
*/
class MailerException extends \Exception {

	/**
	 * Argument is not a correctly formatted email.
	 * @var int
	 */
	const INVALID_EMAIL = 1;
}

?>