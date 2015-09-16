<?php

namespace JNMFW\classes\cache;

class CacheRequest implements ICache {
	private $data = array();
	
	public static function isEnabled() {
		return true;
	}
	
	public function set($key, $value, $ttl = null) {
		$this->data[$key] = $value;
		return true;
	}
	
	public function add($key, $value, $ttl = null) {
		if (!$this->exists($key)) return $this->set($key, $value, $ttl);
		return false;
	}
	
	public function get($key) {
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}
	
	public function setMulti($items, $ttl = null) {
		$ok = true;
		foreach ($items as $key => $value) {
			$ok &= $this->set($key, $value, $ttl);
		}
		return $ok;
	}
	
	public function getMulti($keys) {
		$out = array();
		foreach ($keys as $key) {
			$out[] = $this->get($key);
		}
		return $out;
	}
	
	public function exists($key) {
		return isset($this->data[$key]);
			//&& $this->get($key) !== null; //el resto de ICache comprueba que no sea falso
	}
	
	public function delete($key) {
		if ($this->exists($key)) {
			unset($this->data[$key]);
			return true;
		}
		else {
			return false;
		}
	}
	
	public function deleteMulti($keys) {
		$ok = true;
		foreach ($keys as $key) {
			$ok &= $this->delete($key);
		}
		return $ok;
	}
	
	public function clear() {
		unset($this->data);
		$this->data = array();
	}
}