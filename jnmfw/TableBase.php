<?php

namespace JNMFW;

use JNMFW\classes\databases\DBConnection;
use JNMFW\classes\databases\DBFactory;

abstract class TableBase
{
	private $cols = null;

	static private $dummyItems = array();

	abstract public function getTableName();

	abstract public function getPrimaryKey();

	public function __sleep() {
		return $this->getColumns();
	}

	private function getColumns() {
		if ($this->cols === null) {
			$this->cols = array_keys(array_diff_key(get_object_vars($this), get_class_vars(__CLASS__)));
		}
		return $this->cols;
	}

	private function getValues() {
		$tmp = array();
		foreach ($this->getColumns() as $col) {
			$tmp[$col] = $this->$col;
		}
		return $tmp;
	}

	private function getClassName() {
		return get_class($this);
	}

	public function insert() {
		$db = static::getDB();
		$ok = 1 == $db->getQueryBuilderInsert($this->getTableName())
				->columns($this->getColumns())
				->data($this->getValues())
				->execute();
		if ($ok) {
			$pk = $this->getPrimaryKey();
			if ($this->$pk === null) {
				$this->$pk = $db->getLastInsertedId();
			}
		}
		return $ok;
	}

	public function fill($obj) {
		foreach ($this->getColumns() as $col) {
			$this->$col = $obj->$col;
		}
	}

	public function delete() {
		$db = static::getDB();
		$pk = $this->getPrimaryKey();
		$ok = 1 == $db->getQueryBuilderDelete($this->getTableName())
				->where($pk, $this->$pk)
				->execute();
		return $ok;
	}

	public function update() {
		$db = static::getDB();
		$pk = $this->getPrimaryKey();
		$ok = 1 == $db->getQueryBuilderUpdate($this->getTableName())
				->set($this->getValues())
				->where($pk, $this->$pk)
				->execute();
		return $ok;
	}

	// STATIC

	/**
	 * @return TableBase
	 */
	public static function get($id) {
		$db = static::getDB();
		return $db->getQueryBuilderSelect(self::_getTableName())
			->where(self::_getPrimaryKey(), $id)
			->loadObject(self::_getClassName());
	}

	/**
	 * @return TableBase[]
	 */
	public static function getMulti($ids) {
		if (!$ids) {
			return array();
		}

		$pk = self::_getPrimaryKey();

		$db = static::getDB();
		return $db->getQueryBuilderSelect(self::_getTableName())
			->whereIn($pk, $ids)
			->loadObjectList(self::_getClassName());
	}

	/**
	 * @return TableBase[]
	 */
	public static function getAll() {
		$db = static::getDB();
		return $db->getQueryBuilderSelect(self::_getTableName())
			->loadObjectList(self::_getClassName());
	}

	public static function getAllIDs() {
		$pk = self::_getPrimaryKey();
		$db = static::getDB();
		return $db->getQueryBuilderSelect(self::_getTableName())
			->columns($pk)
			->loadValueArray();
	}

	/**
	 * @return TableBase
	 */
	static protected function getDummyItem() {
		$className = get_called_class();
		if (!isset(self::$dummyItems[$className])) {
			self::$dummyItems[$className] = new $className;
		}
		return self::$dummyItems[$className];
	}

	static protected function _getTableName() {
		return self::getDummyItem()->getTableName();
	}

	static protected function _getPrimaryKey() {
		return self::getDummyItem()->getPrimaryKey();
	}

	static protected function _getClassName() {
		return self::getDummyItem()->getClassName();
	}

	/**
	 * @return DBConnection
	 */
	protected static function getDB() {
		return DBFactory::getInstance();
	}
}
