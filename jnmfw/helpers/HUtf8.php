<?php

namespace JNMFW\helpers;

abstract class HUtf8 {
	static public function is_utf8($str) {
		return mb_detect_encoding($str, 'UTF-8', true) !== false;
	}
	
	static public function encode_safe($str) {
		return static::is_utf8($str) ? $str : utf8_encode($str);
	}
	
	static public function decode_safe($str) {
		return static::is_utf8($str) ? utf8_decode($str) : $str;
	}
	
	static public function strlen($str) {
		return mb_strlen($str, 'UTF-8');
	}
	
	static public function substr($str, $start, $length = null) {
		return mb_substr($str, $start, $length, 'UTF-8');
	}
}