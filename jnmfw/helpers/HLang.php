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
	
	public static function getUserLang() {
		return static::$lang;
	}
	
	public static function toJS($folder, $keys = null) {
		if (!is_array($keys)) {
			$class = '\\'.static::$namespace.'\Lang';
			$refl = new \ReflectionClass($class);
			$keys = $refl->getConstants();
		}
		sort($keys);
		$md5 = md5(constant('\\'.static::$namespace.'\Lang'.static::$lang.'::_VERSION').'-'.
				constant('\\'.static::$namespace.'\Lang'.static::$default.'::_VERSION').'-'.
				print_r($keys, true));
		$filename = strtolower(static::$lang).'-'.$md5.'.js';
		$file = $folder.'/'.$filename;
		if (file_exists($file)) return $filename;
		$data = array();
		foreach ($keys as $key) {
			$data[$key] = static::get($key);
		}
		file_put_contents($file, 'lang = '.json_encode($data).';');
		return $filename;
	}
}
