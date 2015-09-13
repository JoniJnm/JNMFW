<?php

namespace JNMFW;

use JNMFW\classes\databases\DBConnection;

abstract class BaseModel {
	/**
	 * @var DBConnection
	 */
	protected $db;
	protected static $instances = array();
	protected static $tableName;
	
	private $objs = array();
	
	protected function __construct() {
		$this->db = \JNMFW\classes\databases\DBFactory::getInstance();
	}
	
	protected function getByPrimaryKey($id, $tableName, $objName) {
		if (!isset($this->objs[$objName])) {
			$this->objs[$objName] = array();
		}
		$dirty = !isset($this->objs[$objName][$id]) || $this->objs[$objName][$id]->getItem()->isDirty();
		if ($dirty) {
			$item = $tableName::get($id);
			if (!$item) return null;
			$this->objs[$objName][$id] = new $objName($item);
		}
		return $this->objs[$objName][$id];
	}
	
	protected function getMultiByPrimaryKey($ids, $tableName, $objName) {
		if (!isset($this->objs[$objName])) {
			$this->objs[$objName] = array();
		}
		$out = array();
		$dirtys = array();
		foreach ($ids as $id) {
			$dirty = !isset($this->objs[$objName][$id]) || $this->objs[$objName][$id]->getItem()->isDirty();
			if ($dirty) {
				$dirtys[] = $id;
			}
			else {
				$out[] = $this->objs[$objName][$id];
			}
		}
		
		if ($dirtys) {
			$items = $tableName::getMulti($dirtys);
			foreach ($items as $item) {
				if (!$item) continue;
				$obj = new $objName($item);
				$out[] = $obj;
				$pk = $item->_getPrimaryKey();
				$id = $item->$pk;
				$this->objs[$objName][$id] = $obj;
			}
		}
		
		return $out;
	}
	
	public static function getInstance() {
		$caller = get_called_class();
		if (!isset(static::$instances[$caller])) 
			static::$instances[$caller] = new static;
		return static::$instances[$caller];
	}
}
