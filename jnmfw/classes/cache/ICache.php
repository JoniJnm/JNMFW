<?php

namespace JNMFW\classes\cache;

interface ICache {
	public function set($key, $value, $ttl = null);
	public function add($key, $value, $ttl = null);
	public function get($key);
	public function exists($key);
	public function delete($key);
	public function setMulti($items, $ttl = null);
	
	/**
	 * Retreive items
	 * @param string[] $keys array of keys to retreive
	 * @return mixed[] results. The key of the array must be key to retreive
	 */
	public function getMulti($keys);
	public function deleteMulti($keys);
	public function clear();
}