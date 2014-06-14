<?php

namespace JNMFW\classes\cache;

/**
 * Para MemCache
 */

class CacheGlobal implements ICache {
	private static $instance = null;
	
	private function __construct() {
		
	}
	
	/**
	 * @return CacheGlobal
	 */
	public static function getInstance() {
		if (static::$instance === null)
			static::$instance = new static();
		return static::$instance;
	}
	
	public function isEnabled() {
		return false;
	}
	
	public function delete($id) {

	}

	public function exists($id) {
		return false;
	}

	public function load($id) {
		return null;
	}

	public function save($id, $data) {
		
	}
}
