<?php

namespace JNMFW\helpers;

abstract class HUtils {
	static public function createToken() {
		return \sha1(\mt_rand());
	}
	
	static public function getNullDate() {
		return '0000-00-00';
	}
	
	static public function getNullDateTime() {
		return '0000-00-00 00:00:00';
	}
	
	static public function formatDate($date) {
		return \JNMFW\classes\databases\DBFactory::getInstance()->formatDate($date);
	}
	
	static public function formatDateTime($date) {
		return \JNMFW\classes\databases\DBFactory::getInstance()->formatDateTime($date);
	}
}
