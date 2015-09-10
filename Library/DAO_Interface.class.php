<?php

namespace Library;

interface DAO_Interface {
	public static function getConnexion();
	public static function beginTransaction();
	public static function commitTransaction();
	public static function rollBack();
}

?>