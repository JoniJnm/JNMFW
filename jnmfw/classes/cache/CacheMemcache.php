<?php

namespace JNMFW\classes\cache;

use JNMFW\exceptions\JNMException;

class CacheMemcache implements ICache {
	/**
	 * @var \Memcached
	 */
	private $obj;
	
	function __construct($hosts) {
		$this->obj = new \Memcached();
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
				throw new JNMException("Can't add memcache server");
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
		//las keys del array devuelto se corresponde con la key a obtener
		return $this->obj->getMulti($keys);
	}
	
	public function exists($key) {
		return $this->obj->get($key) !== null;
	}
	
	public function delete($key) {
		return $this->obj->delete($key);
	}
	
	public function deleteMulti($keys) {
		return $this->obj->deleteMulti($keys);
	}
	
	public function clear() {
		return $this->obj->flush();
	}
}