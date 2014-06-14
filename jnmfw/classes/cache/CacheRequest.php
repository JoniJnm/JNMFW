<?php

namespace JNMFW\classes\cache;

class CacheRequest implements ICache {
	private static $instance = null;
	private $data = array();
	
	private function __construct() {
		
	}
	
	/**
	 * @return CacheRequest
	 */
	public static function getInstance() {
		if (static::$instance === null)
			static::$instance = new static();
		return static::$instance;
	}
	
	public function delete($id) {
		unset($this->data[$id]);
	}

	public function exists($id) {
		return isset($this->data[$id]);
	}

	public function load($id) {
		return $this->data[$id];
	}

	public function save($id, $data) {
		$this->data[$id] = $data;
	}
}
