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
class PDOException extends \Exception {

	/**
	 * Attempting to obtain entry from invalid id
	 * @var int
	 */
	const INVALID_ID = 1;
	
	/**
	 * Query failed to execute
	 * @var int
	 */
	const QUERY_FAIL = 2;
	
	/**
	 * Query returned empty result
	 * @var int
	 */
	const EMPTY_RESULT = 3;
	
	/**
	 * Attempting to obtain entry from an invalid object instance
	 * @var unknown
	 */
	const INVALID_KEY = 4;
}

?>