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
	
	public static function getUserLang() {
		return static::$lang;
	}
	
	public static function toJS($folder) {
		$filename = strtolower(static::$lang).'-'.
				constant('\\'.static::$namespace.'\Lang'.static::$lang.'::VERSION').'-'.
				constant('\\'.static::$namespace.'\Lang'.static::$default.'::VERSION').'.js';
		$file = $folder.'/'.$filename;
		if (file_exists($file)) return $filename;
		$class = '\\'.static::$namespace.'\Lang';
		$refl = new \ReflectionClass($class);
		$consts = $refl->getConstants();
		$data = array();
		foreach ($consts as $key) {
			$data[$key] = static::get($key);
		}
		file_put_contents($file, 'lang = '.json_encode($data).';');
		return $filename;
	}
}
