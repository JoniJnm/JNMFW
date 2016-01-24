<?php

namespace JNMFW;

use JNMFW\classes\cache\CacheManager;
use JNMFW\exceptions\JNMException;

abstract class TableCached extends TableBase {
	private $dirty = false;
	
	public function isDirty() {
		return $this->dirty;
	}
	
	public function insert() {
		$ok = parent::insert();
		return $ok && $this->cacheUpdate();
	}
	
	public function delete() {
		$ok = parent::delete();
		return $ok && $this->cacheDelete();
	}
	
	public function update() {
		$ok = parent::update();
		return $ok && $this->cacheUpdate();
	}
	
	private function cacheDelete() {
		$this->dirty = true;
		$cache = self::getCache();
		$key = $this->getKeyCache();
		return $cache->delete($key);
	}
	
	private function cacheUpdate() {
		if ($this->dirty) {
			throw new JNMException("Trying to store a dirty object");
		}
		$cache = self::getCache();
		$key = $this->getKeyCache();
		return $cache->set($key, $this);
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
		
		$item = parent::get($id);
		if ($item) {
			$cache->set($key, $item);
		}
		return $item;
	}
	
	public static function getMulti($ids) {
		if (!$ids) return array();
		
		$pk = self::_getPrimaryKey();
		
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
		
		if ($ids) {
			$more = parent::getMulti($ids);
			$items = array();
			foreach ($more as $item) {
				$key = $item->getKeyCache();
				$items[$key] = $item;
			}
			$cache->setMulti($items);
			
			$out = array_merge($out, $more);
		}
		
		return $out;
	}
	
	public static function getAll() {
		$ids = $this->getAllIDs();
		return $this->getMulti($ids);
	}
	
	/**
	 * @return CacheManager
	 */
	private static function getCache() {
		return CacheManager::getInstance();
	}
}
