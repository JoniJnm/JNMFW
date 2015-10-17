<?php

namespace JNMFW\helpers;

abstract class HUtils {
	static public function createToken() {
		return \sha1(\mt_rand());
	}
	
	static public function getClientIP() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		elseif (!empty($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
		}
		else {
			return null;
		}
	}
}
