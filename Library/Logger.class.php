<?php

namespace Library;

if (!defined("EVE_APP"))
	exit();

/**
 * Class Logger.
 * This class is provided to log different information on different file.
 * 
 * The information are logged, and could be easy retrieved by an admin.
 * There is different paths for the different files, instead of only one file that logs everything.
 * More than that, the logger provides different granularity to save the file by year, month or all time in the same file.
 * 
 * This class extends {@see \Library\ApplicationComponent}
 * 
 * @see \Library\ApplicationComponent
 * 
 * @copyright ParaGP Swizerland
 * @author Zellweger Vincent
 * @author Toudoudou
 * @version 1.0
 */
class Logger extends ApplicationComponent{
	/**
	 * The path to the depot from the root.
	 * 
	 * The depot is the root path for the different log. All the log architecture will be in this folder.
	 * 
	 * @var string
	 */
	private $depot;
	
	/**
	 * A granularity to put all the log of the same type on the same file.
	 * 
	 * The file path is independent of any passing of time.
	 * 
	 * @var string
	 */
	const GRAN_VOID = "VOID";
	
	/**
	 * A granularity to regroup the log of the same month in specific file.
	 * 
	 * Each month, a new folder is created to regroup those files.
	 * 
	 * @var string
	 */
	const GRAN_MONTH = "MONTH";


	/**
	 * A granularity to regroup the log of the same year in specific file.
	 * 
	 * Each year, a new folder is created to regroup those files.
	 *
	 * @var string
	 */
	const GRAN_YEAR = "YEAR";
	
	/**
	 * Constructor of the logger.
	 * 
	 * Given the path of the depot, the constructor check whether this path exists and if not, it tries to create it.
	 * 
	 * Since this class is a subclass of {@see \Library\ApplicationComponent} it should call the parent constructor with the {@see \Library\Application} param.
	 * 
	 * @param \Library\Application $app
	 * @param string $path
	 * 				Path of the depot where all the different log will be written.
	 * @throws \InvalidArgumentException
	 * 				Throws an exception if the dir provided by $path doesn't exist and it's not possible to create it.
	 */
	public function __construct(\Library\Application $app, $path) {
		parent::__construct($app);
		
		$path .= "/" . $app->name();
		
		if (!is_dir($path))
			if (!mkdir($path, 0770, true))
				throw new \InvalidArgumentException("Unable to write in [" . $path . "]");
			
		$this->depot = realpath($path);
	}
	
	/**
	 * Method that provides the path of the log file.
	 * 
	 * Given the type of the log and the name of the error group, this function will create the different folders.
	 * It also checks the granularity to add or not a folder depending on the granularity.
	 * 
	 * @param string $type
	 * @param string $name
	 * @param string $gran
	 * 
	 * @throws \InvalidArgumentException
	 * 			If the type is not provided or if the name is empty.
	 * @throws \RuntimeException
	 * 			If the path dosn't exist and we can't create it.
	 * 
	 * @return string
	 */
	public function path($type, $name, $gran = self::GRAN_YEAR) {
		if (!isset($type) || empty($name) || !is_string($name))
			throw new \InvalidArgumentException("The type [" . $type . "] and the name [" . $name . "] have to be valid");
		
		if (empty($type))
			$type_path = $this->depot . "/";
		else {
			$type_path = $this->depot . "/" . $type . "/";
			if (!is_dir($type_path))
				if (!mkdir($type_path, 0770, true))
					throw new \RuntimeException("Unable to write in [" . $type_path . "]");
		}
		
		switch ($gran) {
			case self::GRAN_VOID:
				$path = null;
				$logFile = $type_path . $name . "_" . time() . ".log";
				break;
			case self::GRAN_MONTH:
				$path = $type_path . date("Ym");
				break;
			case self::GRAN_YEAR:
			default:
				$path = $type_path . date("Y");
		}
		
		if ($path != null) {
			if (!is_dir($path))
				if (!mkdir($path, 0770, true))
					throw new \RuntimeException("Unable to write in [" . $path . "]");
			$logFile = $path . "/" . $name . ".log";
		}
		
		return $logFile;
	}
	
	/**
	 * Function that is called to write the log.
	 * Function that gets the path from the type, the name and the granularity and creates the different information using in the log fie.
	 * 
	 * The different row contains
	 * 
	 * First line
	 * 
	 * - An ID for the error, provided by {@see time()}
	 * - the date of the error
	 * - the file and the line of the error
	 * 
	 *  Second line
	 *  
	 *  - The IP of the host
	 *  - The lang of the user
	 *  - The session ID
	 *  - Whether the user is authenticated or not
	 *  - The admin lvl (empty if not authenticated)
	 *  - The ID of the user (empty if not authenticated)
	 * 
	 * Third line
	 * 
	 * - The information provided by the row parameter
	 * 
	 *  Then we have a separation row
	 * 
	 * @param string $type
	 * 		Type of log
	 * @param string $name
	 * 		Name of the group of log
	 * @param string $row
	 * 		Information to be written for the log information
	 * @param string $file
	 * 		File in which the log has been called
	 * @param string $line
	 * 		Line where the log has been called
	 * @param bool $debug
	 * 		If true, then add all the backtrack information to the log
	 * @param string $gran
	 * 		The granularity of the log
	 * 
	 * @throws \InvalidArgumentException
	 *			If the informations are missing such that the log wouldn't be correct
	 * @return number
	 * 			The ID of the log
	 */
	public function log($type, $name, $row, $file, $line, $debug = true, $gran = self::GRAN_YEAR) {
		if (!isset($type) || empty($name) || empty($row) || empty($file) || empty($line))
			throw new \InvalidArgumentException("The type [" . $type . "], the name [" . $name . "], the row [" . $row . "]"
					. ", the file [" . $file . "] and the line [" . $line . "] has to be valid");
		
		$id = time();
		$ret = $type . "-" . $name . ": " . $row;
		
		$row = "ID: " . $id . " - " . date('d/m/Y H:i:s')." - in file " . $file . " [" . $line. "]\r\n"
				. "IP: " . $this->app()->user()->getIP() . " - Lang: " . $this->app()->user()->getLanguage() . " - Session: " . $this->app()->user()->getSessId()
				. " - Authenticated: " . $this->app()->user()->isAuthenticated() . " - Admin " . $this->app()->user()->getAdminLvl() ." - ID: " . $this->app()->user()->id() . "\r\n"
						. $row . "\r\n";
		
		if ($debug) {
			$backtrack = array();
				
			foreach (debug_backtrace() AS $trace) {
				$args = array();
				foreach ($trace["args"] AS $a) {
					if (is_object($a))
						$args[] = get_class($a);
					elseif (is_resource($a))
						$args[] = "[ressource]";
					elseif (is_array($a))
						$args[] = "array";
					else
						$args[] = $a;
				}
		
				$file = "";
				if (key_exists("file", $trace) && key_exists("line", $trace))
					$file .= $trace["file"] . " [" . $trace["line"] . "]";
		
				$func = $trace["function"];
				if (key_exists("type", $trace) && key_exists("class", $trace))
					$func = $trace["class"] . $trace["type"] . $func;
		
				$backtrack[] = "{File " . $file . " - " . $func . "(" . implode(", ", $args) . ")}";
			}
		
			if (!preg_match('#\r\n$#',$row))
				$row .= "\r\n";
				
			$row = $debug ? (implode(" <- ", $backtrack) . "\r\n" . $row) : $row;
		}
		
		if (\Library\Application::appConfig()->getConst("DEBUG") || !\Library\Application::appConfig()->getConst("LOG")) {
			return $row;
		} else {
			$row .= "---------------------------------------------------------------------------------------\r\n";
			
			$logFile = $this->path($type, $name, $gran);
			$this->write($logFile, $row);
			
			return $ret . "\r\nCheck the log for error ID " . $id . " for more details.";
		}
	}
	
	/**
	 * Function that writes the log.
	 * Given the logFile and the row, writes the row at the end of the file
	 * 
	 * @param string $logfile
	 * 			The path from the root to the log file
	 * @param string $row
	 * 			All the information that we want for this log
	 * @throws \InvalidArgumentException
	 * 			If it misses some information or if the file is not provided
	 */
	private function write($logfile, $row){
		if (empty($logfile))
			throw new \InvalidArgumentException("The logfile [" . $logfile . "] has to be valid.");
		
		$fichier = @fopen($logfile,'a+');
		
		if ($fichier === false || fputs($fichier, $row) === false)
			throw new \InvalidArgumentException("The file provided is not writable.");
		
		fclose($fichier);
	}
	
}

?>