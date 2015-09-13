<?php

namespace JNMFW\classes\cache;

class CacheMemcache implements ICache {
	/**
	 * @var Memcached
	 */
	private $obj;
	
	function __construct($hosts) {
		$this->obj = new Memcached();
		if (!is_array($hosts)) {
			$hosts = array($hosts);
		}
		foreach ($hosts as $host) {
			if (strpos($host, ':') !== false) {
				$host = explode(':', $host);
				$ip = $host[0];
				$port = $host[1];
			}
			else {
				$ip = $host;
				$port = 6379;
			}
			if ($this->obj->addServer($ip, $port)) {
				throw new Exception("Can't add memcache server");
			}
		}
	}
	
	public static function isEnabled() {
		return extension_loaded('Memcached');
	}
	
	public function set($key, $value, $ttl = null) {
		return $this->obj->set($key, $value, $ttl);
	}
	
	public function add($key, $value, $ttl = null) {
		return $this->obj->add($key, $value, $ttl);
	}
	
	public function get($key) {
		return $this->obj->get($key);
	}
	
	public function setMulti($items, $ttl = null) {
		return $this->obj->setMulti($items, $ttl);
	}
	
	public function getMulti($keys) {
		return $this->obj->getMulti($keys);
	}
	
	public function exists($key) {
		return $this->obj->get($key) !== false;
	}
	
	public function delete($key) {
		return $this->obj->delete($key);
	}
	
	public function deleteMulti($keys) {
		return $this->obj->deleteMulti($keys);
	}
	
	public function clear() {
		$prefix = ENTORNO.'-';
		$keys = $this->obj->getAllKeys();
		$remove = array();
		foreach ($keys as $key) {
			if (strpos($key, $prefix) === 0) {
				$remove[] = $key;
			}
		}
		if (!count($remove)) return false;
		return !!$this->obj->deleteMulti($remove);
	}
}