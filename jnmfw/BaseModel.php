<?php

namespace JNMFW;

use JNMFW\classes\databases\Database;

abstract class BaseModel {
	/**
	 * @var Database
	 */
	protected $db;
	protected static $instances = array();
	
	protected function __construct() {
		$this->db = \JNMFW\classes\databases\DBFactory::getInstance();
	}
	
	public static function getInstance() {
		$caller = get_called_class();
		if (!isset(static::$instances[$caller])) 
			static::$instances[$caller] = new static;
		return static::$instances[$caller];
	}
}
