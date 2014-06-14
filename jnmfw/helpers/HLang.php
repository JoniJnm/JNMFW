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
	
	public static function get($key) {
		if (defined('\\'.static::$namespace.'\Lang'.static::$lang.'::'.$key)) {
			return constant('\\'.static::$namespace.'\Lang'.static::$lang.'::'.$key);
		}
		elseif (defined('\\'.static::$namespace.'\Lang'.static::$default.'::'.$key)) {
			HLog::logWarning("Clave de idioma '$key' no traducida para ".static::$lang);
			return constant('\\'.static::$namespace.'\Lang'.static::$default.'::'.$key);
		}
		else {
			HLog::logWarning("Clave de idioma '$key' no definida");
			return $key;
		}
	}
}
