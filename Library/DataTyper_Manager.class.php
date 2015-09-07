<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * The parent class for the DataType.
 * 
 * Given a module and a model, it should list all the types of data.
 * 
 * For each data, it needs to give
 * 
 * - If the value is null or not
 * - The type of the data (string, int, ...)
 * - The default value of the data
 * - If the data is a key or not
 *
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @version 1.0
 * @abstract
 */
abstract class DataTyper_Manager{

	/**
	 * Has to return an array that contains the different informations about the datatype
	 * 
	 * - null => NO|YES
	 * - Type => String
	 * - Default => NULL|String|int|bool
	 * - Key => Pri|
	 * 
	 * TODO: change that to make it more confortable for changing it in anything else than SQL
	 * 
	 * @param string $module
	 * 		The name of the module in which the model is
	 * 
	 * @param unknown $model
	 * 		The name of the model of which we need the dataType
	 * 
	 * @return string[]
	 */
	abstract public function getDataType($module, $model);
}

?>