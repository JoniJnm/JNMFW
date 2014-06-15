<?php

namespace JNMFW;

use JNMFW\classes\databases\Database;
use JNMFW\classes\cache\CacheGlobal;

abstract class BaseTable {
	private $tableName;
	private $cols = null;
	private $primaryKey;
	private $cache;
	
	abstract static protected function getTableName();
	abstract static protected function getPrimaryKey();
	abstract static protected function isCacheEnabled();
	
	public function __construct() {
		$this->tableName = static::getTableName();
		$this->primaryKey = static::getPrimaryKey();
		$this->cache = static::isCacheEnabled();
	}
	
	private function getColums() {
		if ($this->cols === null) {
			$this->cols = array_keys(array_diff_key(get_object_vars($this), get_class_vars(__CLASS__)));
		}
		return $this->cols;
	}
	
	static private function getIDCache($idTable) {
		if (!$idTable) helpers\HServer::sendServerError("Can't get cache ID because Obj is not ready");
		return 'table_'.static::getTableName().'_'.$idTable;
	}
	
	static protected function getCachedTable($idTable) {
		if (static::isCacheEnabled()) return CacheGlobal::getInstance()->load(static::getIDCache($idTable));
		else return null;
	}
	
	private function getPKValue() {
		$pk = $this->primaryKey;
		return $this->$pk;
	}
	
	/**
	 * @return Database
	 */
	private function getDB() {
		return \JNMFW\classes\databases\DBFactory::getInstance();
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
		$db = $this->getDB();
		$ok = 1 == $db->getQueryBuilderInsert($this->tableName)
				->columns($this->getColums())
				->values($this->getValues(false))
				->execute();
		if ($ok) {
			$pk = $this->primaryKey;
			if ($this->$pk === null) {
				$this->$pk = $db->getLastInsertedId();
			}
			if ($this->cache) {
				CacheGlobal::getInstance()->save(static::getIDCache($this->getPKValue()), $this);
			}
		}
		return $ok;
	}
	
	public function load($id) {
		$db = $this->getDB();
		$obj = $db->getQueryBuilderSelect($this->tableName)->columns('*')->where($this->primaryKey, $id)->loadObject();
		if (!$obj) return false;
		foreach ($this->getColums() as $col) {
			$this->$col = $obj->$col;
		}
		return true;
	}
	
	public function delete() {
		if ($this->cache) {
			CacheGlobal::getInstance()->delete(static::getIDCache($this->getPKValue()));
		}
		$db = $this->getDB();
		$pk = $this->primaryKey;
		return 1 == $db->getQueryBuilderDelete($this->tableName)
				->where($pk, $this->$pk)
				->execute();
	}
	
	public function update() {
		if ($this->cache) {
			CacheGlobal::getInstance()->save(static::getIDCache($this->getPKValue()), $this);
		}
		$db = $this->getDB();
		$pk = $this->primaryKey;
		return 1 == $db->getQueryBuilderUpdate($this->tableName)
				->set($this->getValues(true))
				->where($pk, $this->$pk)
				->execute();
	}
}
