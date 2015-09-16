<?php

namespace JNMFW\classes\cache;

class CacheXCache implements ICache {
	public static function isEnabled() {
		return extension_loaded('XCache');
	}
	
	public function set($key, $value, $ttl = null) {
		return xcache_set($key, serialize($value), $ttl);
	}
	
	public function add($key, $value, $ttl = null) {
		if (!xcache_isset($key)) {
			return $this->set($key, $value, $ttl);
		}
		return false;
	}
	
	public function get($key) {
		$data = xcache_get($key);
		if ($data === null) return null;
		return unserialize($data);
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
			$out[] =  $this->get($key);
		}
		return $out;
	}
	
	public function exists($key) {
		return xcache_isset($key);
	}
	
	public function delete($key) {
		return xcache_unset($key);
	}
	
	public function deleteMulti($keys) {
		$ok = true;
		foreach ($keys as $key) {
			$ok &= $this->delete($key);
		}
		return $ok;
	}
	
	public function clear() {
		return xcache_clear_cache(XC_TYPE_VAR);
	}
}