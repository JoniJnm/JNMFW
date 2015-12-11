<?php

namespace JNMFW;

use JNMFW\classes\databases\DBConnection;
use JNMFW\classes\cache\CacheManager;
use JNMFW\exceptions\JNMException;

abstract class BaseTable {
	private $cols = null;
	private $dirty = false;
	
	static private $dummyItems = array();
	
	abstract public function getTableName();
	abstract public function getPrimaryKey();
	public function useCache() {
		return true;
	}
	
	public function __construct() {
		
	}
	
	public function __sleep() {
		return $this->getColumns();
	}
	
	private function getColumns() {
		if ($this->cols === null) {
			$this->cols = array_keys(get_class_vars(get_class($this)));
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
		$this->cacheUpdate();
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
		$this->cacheDelete();
		return $ok;
	}
	
	public function update() {
		$db = static::getDB();
		$pk = $this->getPrimaryKey();
		$ok = 1 == $db->getQueryBuilderUpdate($this->getTableName())
				->set($this->getValues())
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
		if ($this->useCache()) {
			$cache = self::getCache();
			$key = $this->getKeyCache();
			$cache->delete($key);
		}
	}
	
	private function cacheUpdate() {
		if ($this->dirty) {
			throw new JNMException("Trying to store a dirty object");
		}
		if ($this->useCache()) {
			$cache = self::getCache();
			$key = $this->getKeyCache();
			$cache->set($key, $this);
		}
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
		if (self::_useCache()) {
			$cache = self::getCache();
			$key = self::getKeyCacheByID($id);
			$item = $cache->get($key);
			if ($item) return $item;
		}
		
		$db = static::getDB();
		$obj = $db->getQueryBuilderSelect(self::_getTableName())
				->where(self::_getPrimaryKey(), $id)
				->loadObject();
		if (!$obj) return null;
		
		$item = new static;
		$item->fill($obj);
		if (self::_useCache()) {
			$cache->set($key, $item);
		}
		
		return $item;
	}
	
	public static function getMulti($ids) {
		$pk = self::_getPrimaryKey();
		
		if (self::_useCache()) {
			$cache = self::getCache();

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
		}
		
		if ($ids) {
			$db = static::getDB();
			$objs = $db->getQueryBuilderSelect(self::_getTableName())
					->whereIn($pk, $ids)
					->loadObjectList();
			
			$items = array();
			foreach ($objs as $obj) {
				$item = new static;
				$item->fill($obj);
				$out[] = $item;
				
				if (self::_useCache()) {
					$key = $item->getKeyCache();
					$items[$key] = $item;
				}
			}
			
			if (self::_useCache()) {
				$cache->setMulti($items);
			}
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
	
	static protected function _getTableName() {
		return self::getDummyItem()->getTableName();
	}
	
	static protected function _getPrimaryKey() {
		return self::getDummyItem()->getPrimaryKey();
	}
	
	static protected function _useCache() {
		return self::getDummyItem()->useCache();
	}
	
	/**
	 * @return DBConnection
	 */
	protected static function getDB() {
		return \JNMFW\classes\databases\DBFactory::getInstance();
	}
	
	/**
	 * @return CacheManager
	 */
	private static function getCache() {
		return CacheManager::getInstance();
	}
}
