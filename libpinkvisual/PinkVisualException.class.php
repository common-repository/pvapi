<?php

// $$LICENSE$$

class PinkVisualException extends Exception {
	const GENERAL = 100;
	const NOT_FOUND = 101;
	const CONTENT_ERROR = 102;
	private static $sReturn = true;
	private static $sReturnValue = false;
	public static function ret() {
		return self::$sReturn;
	}
	public static function value() {
		return self::$sReturnValue;
	}
	public static function init($param) {
		if($param === true) {
			self::$sReturn = false;
			self::$sReturnValue = null;
		} else {
			self::$sReturn = true;
			self::$sReturnValue = $param;
		}
	}
}