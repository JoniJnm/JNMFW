<?php

namespace JNMFW;

use JNMFW\classes\databases\DatabaseConnection;

abstract class BaseModel {
	/**
	 * @var DatabaseConnection
	 */
	protected $db;
	protected static $instances = array();
	protected static $tableName;
	
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
