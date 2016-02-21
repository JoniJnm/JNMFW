<?php

namespace JNMFW\helpers;

abstract class HLang {
	private static $lang;
	private static $default;
	private static $namespace;
	
	public static function init($user_lang, $default_lang, $namespace) {
		static::$lang = $user_lang;
		static::$default = $default_lang;
		static::$namespace = $namespace;
	}
	
	public static function get($key, $encodeHTML = true) {
		if (defined('\\'.static::$namespace.'\Lang'.static::$lang.'::'.$key)) {
			$out = constant('\\'.static::$namespace.'\Lang'.static::$lang.'::'.$key);
		}
		elseif (defined('\\'.static::$namespace.'\Lang'.static::$default.'::'.$key)) {
			HLog::warning("Clave de idioma '$key' no traducida para ".static::$lang);
			$out = constant('\\'.static::$namespace.'\Lang'.static::$default.'::'.$key);
		}
		else {
			HLog::warning("Clave de idioma '$key' no definida");
			$out = $key;
		}
		if ($encodeHTML) {
			return htmlspecialchars($out, ENT_QUOTES | ENT_HTML5);
		}
		else {
			return $out;
		}
	}
	
	public static function getr($key, $dic, $encodeHTML = true) {
		$msg = static::get($key, $encodeHTML);
		return str_replace(array_keys($dic), array_values($dic), $msg);
	}
	
	public static function getUserLang() {
		return static::$lang;
	}
}
