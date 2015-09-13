<?php

namespace JNMFW;

use JNMFW\classes\databases\DBConnection;
use JNMFW\classes\cache\CacheManager;

abstract class BaseTable {
	private $tableName;
	private $cols = null;
	private $primaryKey;
	private $dirty = false;
	
	public function _getPrimaryKey() {
		return $this->getPrimaryKey();
	}
	
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
		$this->cacheUpdate();
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
		$ok = 1 == $db->getQueryBuilderDelete($this->tableName)
				->where($pk, $this->$pk)
				->execute();
		$this->cacheDelete();
		return $ok;
	}
	
	public function update() {
		$db = self::getDB();
		$pk = $this->primaryKey;
		$ok = 1 == $db->getQueryBuilderUpdate($this->tableName)
				->set($this->getValues(true))
				->where($pk, $this->$pk)
				->execute();
		$this->cacheUpdate();
		return $ok;
	}
	
	// CACHE
	
	public function isDirty() {
		return $this->dirty;
	}
	
	private function cacheDelete() {
		$this->dirty = true;
		$cache = self::getCache();
		$key = $this->getKeyCache();
		$cache->delete($key);
	}
	
	private function cacheUpdate() {
		if ($this->dirty) {
			throw new Exception("Trying to store a dirty object");
		}
		$cache = self::getCache();
		$key = $this->getKeyCache();
		$cache->set($key, $this);
	}
	
	private function getKeyCache() {
		$pk = $this->getPrimaryKey();
		$id = $this->$pk;
		$prefix = (new \ReflectionClass($this))->getShortName();
		return $prefix.'-'.$id;
	}
	
	private static function getKeyCacheByID($id) {
		$item = self::getDummyItem();
		$prefix = (new \ReflectionClass($item))->getShortName();
		return $prefix.'-'.$id;
	}
	
	// STATIC
	
	public static function get($id) {
		$cache = self::getCache();
		$key = self::getKeyCacheByID($id);
		$item = $cache->get($key);
		if ($item) return $item;
		$db = self::getDB();
		$obj = $db->getQueryBuilderSelect(self::tableName())
				->where(self::primaryKey(), $id)
				->loadObject();
		if (!$obj) return null;
		$item = new static;
		$item->fill($obj);
		$cache->set($key, $item);
		return $item;
	}
	
	public static function getMulti($ids) {
		$cache = self::getCache();
		$pk = self::primaryKey();
				
		$keys = array();
		foreach ($ids as $id) {
			$keys[] = self::getKeyCacheByID($id);
		}
		$out = $cache->getMulti($keys);
		
		$ids_in_cache = array();
		foreach ($out as $item) {
			$ids_in_cache[] = $item->$pk;
		}
		
		$ids = array_diff($ids, $ids_in_cache);
		
		if ($ids) {
			$db = self::getDB();
			$objs = $db->getQueryBuilderSelect(self::tableName())
					->whereIn($pk, $ids)
					->loadObjectList();
			
			$items = array();
			foreach ($objs as $obj) {
				$item = new static;
				$item->fill($obj);
				$key = $item->getKeyCache();
				$items[$key] = $item;
				$out[] = $item;
			}
			$cache->setMulti($items);
		}
		
		return $out;
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
	
	static protected function tableName() {
		return self::getDummyItem()->getTableName();
	}
	
	static protected function primaryKey() {
		return self::getDummyItem()->getPrimaryKey();
	}
	
	static protected function columns() {
		return self::getDummyItem()->getColums();
	}
	
	/**
	 * @return DBConnection
	 */
	private static function getDB() {
		return \JNMFW\classes\databases\DBFactory::getInstance();
	}
	
	/**
	 * @return CacheManager
	 */
	private static function getCache() {
		return CacheManager::getInstance();
	}
}
