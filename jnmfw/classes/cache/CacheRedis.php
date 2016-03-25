<?php

namespace JNMFW\classes\cache;

use JNMFW\exceptions\JNMException;

class CacheRedis implements ICache
{
	/**
	 * @var \Redis
	 */
	private $obj;

	function __construct($hosts, $timeout = 0)
	{
		$this->obj = new \Redis();
		if (!is_array($hosts)) {
			$hosts = array($hosts);
		}
		$connected = false;
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
			if ($this->obj->connect($ip, $port, $timeout)) {
				$connected = true;
				break;
			}
		}
		if (!$connected) {
			throw new JNMException("Can't connect to redis server");
		}
	}

	public static function isEnabled()
	{
		return extension_loaded('redis');
	}

	public function set($key, $value, $ttl = null)
	{
		return $this->obj->set($key, serialize($value), $ttl);
	}

	public function add($key, $value, $ttl = null)
	{
		if ($this->obj->setnx($key, $value)) {
			return $this->set($key, $value, $ttl); //setnx doesn't accept the param ttl
		}
		return false;
	}

	public function get($key)
	{
		$ret = $this->obj->get($key);
		if (!$ret) {
			return null;
		}
		return unserialize($ret);
	}

	public function setMulti($items, $ttl = null)
	{
		//serialize_each($items);
		//return $this->obj->mset($items) //mset doesn't accept the param ttl
		$multi = $this->obj->multi();
		foreach ($items as $key => $value) {
			$multi = $multi->set($key, serialize($value), $ttl);
		}
		return $multi->exec();
	}

	public function getMulti($keys)
	{
		$items = $this->obj->getMultiple($keys);
		$count = count($items);
		$out = array();
		for ($i = 0; $i < $count; $i++) {
			$item = $items[$i];
			$key = $keys[$i];
			if ($item === null) {
				$out[$key] = $item;
			}
			else {
				$out[$key] = unserialize($item);
			}
		}
		return $out;
	}

	public function exists($key)
	{
		return $this->obj->exists($key);
	}

	public function delete($key)
	{
		return $this->obj->delete($key) > 0;
	}

	public function deleteMulti($keys)
	{
		//the function delete accepts an array of keys
		return $this->obj->delete($keys) > 0;
	}

	public function clear()
	{
		return $this->obj->flushAll();
	}
}