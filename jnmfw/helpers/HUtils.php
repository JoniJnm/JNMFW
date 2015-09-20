<?php

namespace JNMFW\helpers;

abstract class HUtils {
	static public function createToken() {
		return \sha1(\mt_rand());
	}
	
	static private function getDB() {
		return \JNMFW\classes\databases\DBFactory::getInstance();
	}
	
	static public function getNullDate() {
		return self::getDB()->getNullDate();
	}
	
	static public function getNullDateTime() {
		return self::getDB()->getNullDateTime();
	}
	
	static public function isNullDate($date) {
		return self::getDB()->isNullDate($date);
	}
	
	static public function formatDate($date) {
		return self::getDB()->formatDate($date);
	}
	
	static public function formatDateTime($date) {
		return self::getDB()->formatDateTime($date);
	}
	
	static public function getClientIP() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
}
