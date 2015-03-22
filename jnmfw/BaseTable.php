<?php

namespace JNMFW;

use JNMFW\classes\databases\DatabaseConnection;

abstract class BaseTable {
	private $tableName;
	private $cols = null;
	private $primaryKey;
	
	static private $dummyItems = array();
	
	abstract protected function getTableName();
	abstract protected function getPrimaryKey();
	
	public function __construct() {
		$this->tableName = $this->getTableName();
		$this->primaryKey = $this->getPrimaryKey();
	}
	
	private function getColums() {
		if ($this->cols === null) {
			$this->cols = array_keys(array_diff_key(get_object_vars($this), get_class_vars(__CLASS__)));
		}
		return $this->cols;
	}
	
	private function getValues($assoc) {
		$tmp = array();
		foreach ($this->getColums() as $col) {
			if ($assoc) $tmp[$col] = $this->$col;
			else $tmp[] = $this->$col;
		}
		return $tmp;
	}
	
	public function insert() {
		$db = self::getDB();
		$ok = 1 == $db->getQueryBuilderInsert($this->tableName)
				->columns($this->getColums())
				->values($this->getValues(false))
				->execute();
		if ($ok) {
			$pk = $this->primaryKey;
			if ($this->$pk === null) {
				$this->$pk = $db->getLastInsertedId();
			}
		}
		return $ok;
	}
	
	public function fill($obj) {
		foreach ($this->getColums() as $col) {
			$this->$col = $obj->$col;
		}
	}
	
	public function delete() {
		$db = self::getDB();
		$pk = $this->primaryKey;
		return 1 == $db->getQueryBuilderDelete($this->tableName)
				->where($pk, $this->$pk)
				->execute();
	}
	
	public function update() {
		$db = self::getDB();
		$pk = $this->primaryKey;
		return 1 == $db->getQueryBuilderUpdate($this->tableName)
				->set($this->getValues(true))
				->where($pk, $this->$pk)
				->execute();
	}
	
	
	// STATIC
	
	public static function get($id) {
		$db = self::getDB();
		$obj = $db->getQueryBuilderSelect(self::tableName())
				->columns('*')
				->where(self::primaryKey(), $id)
				->loadObject();
		if (!$obj) return null;
		$item = new static;
		$item->fill($obj);
		return $item;
	}
	
	/**
	 * @return BaseTable
	 */
	static private function getDummyItem() {
		$className = get_called_class();
		if (!isset(self::$dummyItems[$className])) {
			self::$dummyItems[$className] = new $className;
		}
		return self::$dummyItems[$className];
	}
	
	static private function tableName() {
		return self::getDummyItem()->getTableName();
	}
	
	static private function primaryKey() {
		return self::getDummyItem()->getPrimaryKey();
	}
	
	static private function columns() {
		return self::getDummyItem()->getColums();
	}
	
	/**
	 * @return DatabaseConnection
	 */
	private static function getDB() {
		return \JNMFW\classes\databases\DBFactory::getInstance();
	}
}
