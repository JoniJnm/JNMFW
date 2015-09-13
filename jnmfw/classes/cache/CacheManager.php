<?php

namespace JNMFW\classes\cache;

use JNMFW\helpers\HLog;
use JNMFW\helpers\HTimer;

class CacheManager {
	private static $instance = null;
	
	private $prefix;
	
	private $deletedKeys = array(); //keys deleted during a transaction
	private $numCacheAccesses = 0;
	
	private $default_ttl = 1800; //60*30 (30 mins)
	const DEFAULT_TTL = -1;
	
	private $scope_default = 0; //auxiliary, to calculate SCOPE_DEFAULT
	private $scope_request_default = 0; //auxiliary, to calculate SCOPE_REQUEST_DEFAULT
	
	const SCOPE_DEFAULT = -1; //local or external
	const SCOPE_REQUEST_DEFAULT = -2; //request + (local or external)
	
	const SCOPE_REQUEST = 1; //request
	const SCOPE_LOCAL = 2; //apc
	const SCOPE_EXTERNAL = 4; //memcache
	
	const SCOPE_REQUEST_LOCAL = 3;
	const SCOPE_REQUEST_EXTERNAL = 5;
	const SCOPE_LOCAL_EXTERNAL = 6;
	const SCOPE_ALL = 7;
	
	/**
	 * @var CacheRequest
	 */
	private $request;
	
	/**
	 * @var ICache
	 */
	private $local;
	
	/**
	 * @var ICache
	 */
	private $external;
	
	public function __construct() {
		$this->request = new CacheRequest();
		$this->computeDefaultScope();
	}
	
	/**
	 * @param ICache $driver
	 */
	public function setLocalCache($driver) {
		$this->local = $driver;
		$this->computeDefaultScope();
	}
	
	/**
	 * @param ICache $driver
	 */
	public function setExternalCache($driver) {
		$this->external = $driver;
		$this->computeDefaultScope();
	}
	
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}
	
	public function setDefaultTTL($ttl) {
		$this->default_ttl = $ttl;
	}
	
	private function computeDefaultScope() {
		if ($this->isEnabledExternal()) {
			$this->scope_default = self::SCOPE_EXTERNAL;
			$this->scope_request_default = self::SCOPE_REQUEST_EXTERNAL;
		}
		elseif ($this->isEnabledLocal()) {
			$this->scope_default = self::SCOPE_LOCAL;
			$this->scope_request_default = self::SCOPE_REQUEST_LOCAL;
		}
		else {
			$this->scope_default = self::SCOPE_REQUEST;
			$this->scope_request_default = self::SCOPE_REQUEST;
		}
	}
	
	/**
	 * @return CacheManager
	 */
	static public function getInstance() {
		if (self::$instance == null) {
			self::$instance = new static();
		}
		return self::$instance;
	}
	
	private function computeScope($scope) {
		//change auxiliary value by real
		if ($scope == self::SCOPE_DEFAULT) {
			$scope = $this->scope_default;
		}
		elseif ($scope == self::SCOPE_REQUEST_DEFAULT) {
			$scope = $this->scope_request_default;
		}
		
		//ensure that use some cache
		if (($scope & self::SCOPE_LOCAL && !$this->isEnabledLocal()) ||
				 ($scope & self::SCOPE_EXTERNAL && !$this->isEnabledExternal())) {
			if ($this->isEnabledExternal()) $scope |= self::SCOPE_EXTERNAL;
			elseif ($this->isEnabledLocal()) $scope |= self::SCOPE_LOCAL;
			else $scope |= self::SCOPE_REQUEST;
		}
		if (!$this->useAnyScope($scope)) {
			$scope = self::SCOPE_REQUEST;
		}
		return $scope;
	}
	
	private function computeTTL($ttl) {
		return $ttl < 0 ? $this->default_ttl : $ttl;
	}
	
	/* Check is Enabled */
	
	public function isEnabledRequest() {
		return true;
	}
	
	public function isEnabledLocal() {
		return $this->local != null;
	}
	
	public function isEnabledExternal() {
		return $this->external != null;
	}
	
	/* Check Scope */
	
	private function useRequestScope($scope) {
		return $scope & self::SCOPE_REQUEST;
	}
	
	private function useLocalScope($scope) {
		return $scope & self::SCOPE_LOCAL && $this->isEnabledLocal();
	}
	
	private function useExternalScope($scope) {
		return $scope & self::SCOPE_EXTERNAL && $this->isEnabledExternal();
	}
	
	private function useAnyScope($scope) {
		return $this->useRequestScope($scope) || $this->useLocalScope($scope) || $this->useExternalScope($scope);
	}
	
	/* GET SPECIFIC CACHE */
	
	/**
	 * @return ICache
	 */
	private function getLocal() {
		return $this->local;
	}
	
	/**
	 * @return ICache
	 */
	private function getExternal() {
		return $this->external;
	}
	
	/* ICache */
	
	public function set($key, $value, $ttl = self::DEFAULT_TTL, $scope = self::SCOPE_REQUEST_DEFAULT) {
		$realkey = $this->getRealKey($key);
		$scope = $this->computeScope($scope);
		$ttl = $this->computeTTL($ttl);
		
		if ($value === false) {
			HLog::warning("Storing in cache a FALSE value for " . $realkey);
		}
		
		$this->numCacheAccesses++;
		
		$ret = $this->useAnyScope($scope);
		
		$this->initLog();
		
		if ($this->useRequestScope($scope)) {
			$ret &= $this->request->set($realkey, $value, $ttl);
		}
		if ($this->useLocalScope($scope)) {
			$ret &= $this->getLocal()->set($realkey, $value, $ttl);
		}
		if ($this->useExternalScope($scope)) {
			$ret &= $this->getExternal()->set($realkey, $value, $ttl);
		}
		
		$this->log('set', $key, $ret);
		
		return $ret;
	}
	
	public function add($key, $value, $ttl = self::DEFAULT_TTL, $scope = self::SCOPE_REQUEST_DEFAULT) {
		$realkey = $this->getRealKey($key);
		$scope = $this->computeScope($scope);
		$ttl = $this->computeTTL($ttl);
		
		if ($value === false) {
			HLog::warning("Storing in cache a FALSE value for " . $realkey);
		}
		
		$this->numCacheAccesses++;
		
		$ret = $this->useAnyScope($scope);
		
		$this->initLog();
		
		if ($this->useRequestScope($scope)) {
			$ret &= $this->request->add($realkey, $value, $ttl);
		}
		if ($this->useLocalScope($scope)) {
			$ret &= $this->getLocal()->add($realkey, $value, $ttl);
		}
		if ($this->useExternalScope($scope)) {
			$ret &= $this->getExternal()->add($realkey, $value, $ttl);
		}
		
		$this->log('add', $key, $ret);
		
		return $ret;
	}
	
	public function get($key, $scope = self::SCOPE_REQUEST_DEFAULT) {
		$realkey = $this->getRealKey($key);
		$scope = $this->computeScope($scope);
		
		$value = false;

		if ($this->useRequestScope($scope)) {
			$value = $this->request->get($realkey);
		}
		
		if ($value === false) {
			$this->initLog();
			
			$this->numCacheAccesses++; //sólo aumentar acceso a caché cuando no está en la requests

			if ($this->useLocalScope($scope)) {
				$value = $this->getLocal()->get($realkey);
			}
			if ($value === false && $this->useExternalScope($scope)) {
				$value = $this->getExternal()->get($realkey);
				if ($value !== false && $this->useLocalScope($scope)) {
					$this->getLocal()->set($realkey, $value);
				}
			}
			
			if ($value !== false && $this->useRequestScope($scope)) {
				$this->request->set($realkey, $value);
			}
			
			$this->log('get', $key, gettype($value));
		}
		
		return $value;
	}
	
	public function setMulti($items, $ttl = self::DEFAULT_TTL, $scope = self::SCOPE_REQUEST_DEFAULT) {
		$scope = $this->computeScope($scope);
		$ttl = $this->computeTTL($ttl);
		
		$data = array();
		foreach ($items as $key => $value) {
			$realkey = $this->getRealKey($key);
			$data[$realkey] = $value;
			if ($value === false) {
				HLog::warning("Storing in cache a FALSE value for " . $realkey);
			}
		}
		
		$this->numCacheAccesses++;
		
		$ret = $this->useAnyScope($scope);
		
		$this->initLog();
		
		if ($this->useRequestScope($scope)) {
			$ret &= $this->request->setMulti($data, $ttl);
		}
		if ($this->useLocalScope($scope)) {
			$ret &= $this->getLocal()->setMulti($data, $ttl);
		}
		if ($this->useExternalScope($scope)) {
			$ret &= $this->getExternal()->setMulti($data, $ttl);
		}
		
		$key = count($items) ? 'multi '.implode(',', array_keys($items)) : '';
		$this->log('setMulti', $key, $ret);
		
		return $ret;
	}
	
	//se devuelve un array con los objetos encontrados
	public function getMulti($keys, $scope = self::SCOPE_REQUEST_DEFAULT) {
		$scope = $this->computeScope($scope);
		$ckeys = count($keys);
		$keys = array_unique($keys); //necesario para performance en esta función
		if ($ckeys != count($keys)) {
			FWUtils::log(BlinkFW_LogFile::LEVEL_ERROR, 'Warning: Pidiendo claves duplicadas en getMulti');
		}
		$out = array();
		$map = array(); //asociar key string con índice
		$realKeys = array();
		
		foreach ($keys as $key) {
			$realKeys[] = $this->getRealKey($key);
			$out[] = false; //iniciarlizar salida a false
		}
		
		for ($i=0; $i<count($realKeys); $i++) {
			$map[$realKeys[$i]] = $i; 
		}
		
		$needKeys = array_merge(array(), $realKeys); //array_copy
		
		$save_request = array(); //los que no se encuentren en local ni extental se guardan en la request
		$save_local = array(); //los que no se encuentren en external se guardan en local
		
		$this->numCacheAccesses++;
		$this->initLog();
		
		if ($this->useRequestScope($scope) && count($needKeys)) {
			$void = array();
			$needKeys = $this->getMultiAux($this->request, $needKeys, $out, $map, $void);
		}
		
		if ($this->useLocalScope($scope) && count($needKeys)) {
			$needKeys = $this->getMultiAux($this->getLocal(), $needKeys, $out, $map, $save_request);
		}
		
		if ($this->useExternalScope($scope) && count($needKeys)) {
			$this->getMultiAux($this->getExternal(), $needKeys, $out, $map, $save_local);
		}
		
		if ($this->useLocalScope($scope) && count($save_local)) {
			$this->getLocal()->setMulti($save_local);
		}
		
		$save_request = array_merge($save_request, $save_local);
		if ($this->useRequestScope($scope) && count($save_request)) {
			$this->request->setMulti($save_request);
		}
		
		$key = count($keys) ? 'multi '.implode(',', $keys) : '';
		$this->log('getMulti', $key, gettype($out));
		
		return $out;
	}
	
	private function getMultiAux($cache, $needKeys, &$out, $map, &$save) {
		$aux = $cache->getMulti($needKeys);
		$morekeys = array();
		for ($i=0; $i<count($aux); $i++) {
			$key = $needKeys[$i];
			//el índice puede ser la key (memcache) o numérico (redis)
			if (isset($aux[$i])) $value = $aux[$i];
			elseif (isset($aux[$key])) $value = $aux[$key];
			else $value = false; //es por key (memcache) y no exite
			if ($value === false) {
				$morekeys[] = $key;
			}
			else {
				$save[$key] = $value;
				$j = $map[$key];
				$out[$j] = $value;
			}
		}
		return $morekeys;
	}
	
	public function exists($key, $scope = self::SCOPE_REQUEST_DEFAULT) {
		$realkey = $this->getRealKey($key);
		$scope = $this->computeScope($scope);
		
		/*
		Es mejor llamar a la función get del manager, que además guardará en la request el resultado.
		Esto es bueno para la parte del código donde se comprueba que existe y luego se hace un get().
		Si se hace un exists se necesitará acceder a la caché 2 veces (1 para exists, otra para get)
		*/

		return $this->get($key, $scope) !== false;
	}
	
	public function delete($key, $scope = self::SCOPE_REQUEST_DEFAULT) {
		$realkey = $this->getRealKey($key);
		$scope = $this->computeScope($scope);
		
		$this->numCacheAccesses++; //aumentar acceso a la caché también en el borrado
		
		$ret = $this->useAnyScope($scope);
		
		$this->initLog();
		
		if ($this->useRequestScope($scope)) {
			$ret &= $this->request->delete($realkey);
		}
		if ($this->useLocalScope($scope)) {
			$ret &= $this->getLocal()->delete($realkey);
		}
		if ($this->useExternalScope($scope)) {
			$ret &= $this->getExternal()->delete($realkey);
		}
		
		$this->log('delete', $key, $ret);
		
		if ($this->inTransaction()) {
			$this->addDeletedKey($realkey, $scope);
		}
		
		return $ret;
	}
	
	public function deleteMulti($keys, $scope = self::SCOPE_REQUEST_DEFAULT) {
		$scope = $this->computeScope($scope);
		$realkeys = array();
		foreach ($keys as $key) {
			$realkeys[] = $this->getRealKey($key);
		}
	
		$this->numCacheAccesses++; //aumentar acceso a la caché también en el borrado
	
		$ret = $this->useAnyScope($scope);
	
		$this->initLog();
	
		if ($this->useRequestScope($scope)) {
			$ret &= $this->request->deleteMulti($realkeys);
		}
		if ($this->useLocalScope($scope)) {
			$ret &= $this->getLocal()->deleteMulti($realkeys);
		}
		if ($this->useExternalScope($scope)) {
			$ret &= $this->getExternal()->deleteMulti($realkeys);
		}
	
		$key = count($keys) ? 'multi '.implode(',', $keys) : '';
		$this->log('deleteMulti', $key, $ret);
		
		if ($this->inTransaction()) {
			$this->addDeletedKeys($realkeys, $scope);
		}
	
		return $ret;
	}
	
	public function clear($scope = self::SCOPE_ALL) {
		$scope = $this->computeScope($scope);
		$out = array();
		if ($this->useLocalScope($scope)) {
			$out[get_class($this->getLocal())] = $this->getLocal()->clear();
		}
		if ($this->useExternalScope($scope)) {
			$out[get_class($this->getExternal())] = $this->getExternal()->clear();
		}
		return $out;
	}
	
	public function getKeyCache($objkey) {
		if ($this->exists($objkey.'_key')) {
			$key = $this->get($objkey.'_key');
		}
		else {
			$key = substr(md5(microtime()), 0, 10);
			$this->set($objkey.'_key', $key);
		}
		return $key;
	}
	
	public function deleteKeyCache($objkey, $scope = self::SCOPE_REQUEST_DEFAULT) {
		$realkey = $objkey.'_key';
		if ($this->inTransaction()) {
			$this->addDeletedKey($realkey, $scope);
		}
		return $this->delete($realkey, $scope);
	}
	
	public function getNumCacheAccesses() {
		return $this->numCacheAccesses;
	}
	
	/* PRIVATE */
	
	private function initLog() {
		HTimer::init('Cache');
	}
	
	private function log($action, $key, $result) {
		$val = is_string($result) ? $result : var_export($result, true);
		HTimer::end('Cache', $action.' '.$key.' '.$val);
	}
	
	private function getRealKey($key) {
		$prefix = $this->getPrefixServer();
		if ($prefix) return $prefix.'-'.$key;
		else return $key;
	}
	
	private function getPrefixServer() {
		return $this->prefix;
	}
	
	private function addDeletedKey($key, $scope) {
		//if (strpos($key, '_lock_') !== false) return;
		if (!isset($this->deletedKeys[$scope])) {
			$this->deletedKeys[$scope] = array();
		}
		$this->deletedKeys[$scope][] = $key;
	}
	
	private function addDeletedKeys($keys, $scope) {
		foreach ($keys as $key) {
			$this->addDeletedKey($key, $scope);
		}
	}
	
	/**
	 * Borra las claves que han sido borradas durante una transacción
	 */
	public function clearDeletedKeys() {
		foreach ($this->deletedKeys as $scope => $keys) {
			$this->deleteMulti($keys, $scope);
		}
	}
	
	private function inTransaction() {
		return \JNMFW\classes\databases\DBFactory::getInstance()->inTransaction();
	}
}