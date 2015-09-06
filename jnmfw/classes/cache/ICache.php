<?php

namespace JNMFW\classes\cache;

interface ICache {
	public function set($key, $value, $ttl = DEFAULT_TTL);
	public function add($key, $value, $ttl = DEFAULT_TTL);
	public function get($key);
	public function exists($key);
	public function delete($key);
	public function setMulti($items, $ttl = DEFAULT_TTL);
	public function getMulti($keys);
	public function deleteMulti($keys);
	public function clear();
}