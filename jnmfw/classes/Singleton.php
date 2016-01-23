<?php

namespace JNMFW\classes;

abstract class Singleton {
	protected static $instances = array();
	
	public static function getInstance() {
		$caller = get_called_class();
		if (!isset(static::$instances[$caller])) {
			static::$instances[$caller] = new static;
		}
		return static::$instances[$caller];
	}
}