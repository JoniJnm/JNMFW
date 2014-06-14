<?php

namespace JNMFW\classes\cache;

use JNMFW\helpers\HServer;

class Cache implements ICache {
	private static $instance = null;
	/**
	 * @var CacheGlobal
	 */
	private $cacheGlobal;
	/**
	 * @var CacheRequest
	 */
	private $cacheRequest;
	
	private function __construct() {
		$this->cacheGlobal = CacheGlobal::getInstance();
		$this->cacheRequest = CacheRequest::getInstance();
	}
	
	/**
	 * @return Cache
	 */
	public static function getInstance() {
		if (static::$instance === null)
			static::$instance = new static();
		return static::$instance;
	}

	public function delete($id) {
		$this->cacheGlobal->delete($id);
		$this->cacheRequest->delete($id);
	}

	public function exists($id) {
		return $this->cacheRequest->exists($id) || $this->cacheRequest->exists($id);
	}

	public function load($id) {
		if ($this->cacheRequest->exists($id)) return $this->cacheRequest->load($id);
		elseif ($this->cacheGlobal->exists($id)) return $this->cacheGlobal->load($id);
		else HServer::sendServerError("Imposible cargar de cache $id");
	}

	public function save($id, $data) {
		$this->cacheRequest->save($id, $data);
		if ($this->cacheGlobal->isEnabled()) $this->cacheGlobal->save($id, $data);
	}
}
