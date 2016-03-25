<?php

namespace JNMFW\helpers;

abstract class HLang
{
	static private $dic = array();

	public static function init($iso_lang, $dir) {
		$file = $dir . '/' . $iso_lang . '.php';
		if (file_exists($file)) {
			$_LANG = array();
			include_once($file);
			if (isset($_LANG) && is_array($_LANG)) {
				static::$dic = $_LANG;
			}
		}
	}

	public static function translate($text, $replace = array(), $encodeHTML = true) {
		$hash = md5($text);
		if (isset(static::$dic[$hash])) {
			$text = static::$dic[$hash];
		}
		if ($replace) {
			$text = str_replace(array_keys($replace), array_values($replace), $text);
		}
		if ($encodeHTML) {
			return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5);
		}
		else {
			return $text;
		}
	}
}
