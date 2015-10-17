<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\helpers\HTimer;
use JNMFW\helpers\HServer;
use JNMFW\helpers\HLog;

class MySQLiPoll implements \JNMFW\classes\databases\DBPoll {
	/**
	 * @var MySQLiConnection 
	 */
	private $db;
	
	/**
	 * @var \mysqli[]
	 */
	private $links;
	
	private $queries;
	
	public function __construct(MySQLiConnection $db, $queries) {
		if (!self::isAvaiable()) {
			HServer::sendServerError("Can't make async query, mysqlnd extension is not installed");
		}
		
		$this->db = $db;
		$this->queries = $queries;
		foreach ($queries as $key => $query) {
			$link = $this->db->createNewNativeConnection();
			if ($link->connect_error) {
				HServer::sendServerError('MySQLi Connect error '.$link->connect_error.' ('.$link->connect_errno.')');
			}
			if (!$link->query($query, MYSQLI_ASYNC)) {
				$msg = $link->error.' ('.$link->errno.')';
				HServer::sendServerError($msg);
			}
			$this->links[$key] = $link;
		}
	}
	
	static public function isAvaiable() {
		//check if mysqlnd is installed
		return defined("MYSQLI_ASYNC");
	}
	
	public function wait() {
		HTimer::init('DB Async wait');
		$read = $error = $reject = array();
		do {
			$read = $error = $reject = $this->links;
			mysqli_poll($read, $error, $reject, 1);
			usleep(100);
		}
		while (count($this->links) !== count($read) + count($error) + count($reject));
		HTimer::end('DB Async wait');
	}

	public function free() {
		foreach ($this->links as $link) {
			$link->close();
		}
		$this->links = array();
		$this->queries = array();
	}
	
	public function __destruct() {
		$this->free();
	}
	
	public function execute($key) {
		$res = $this->initAccess($key);
		$this->endAccess($res, $this->getAffectedRows(), $key);
		return $this->getAffectedRows();
	}

	public function loadObject($key){
		$res = $this->initAccess($key);
		$obj = $this->db->_parseObject($res);
		$this->endAccess($res, $obj ? 1 : 0, $key);
		return $obj;
	}

	public function loadObjectList($key, $keycol = null) {
		$res = $this->initAccess($key);
		$array = $this->db->_parseObjectList($res, $keycol);
		$this->endAccess($res, count($array), $key);
		return $array;
	}

	public function loadValue($key, $col = 0) {
		$res = $this->initAccess($key);
		$value = $this->db->_parseValue($res, $col);
		$this->endAccess($res, $value === false ? 0 : 1, $key);
		return $value;
	}

	public function loadValueArray($key, $col = 0) {
		$res = $this->reap($key);
		$ret = $this->db->_parseValueArray($res, $col);
		$res->free();
		$this->freeLinkByQueryKey($key);
		return $ret;
	}
	
	private function freeQueryByKey($key) {
		$link = $this->getLinkByKey($key);
		if (!$link) HServer::sendServerError("Invalid key ".$key);
		$link->close();
		unset($this->links[$key]);
		unset($this->queries[$key]);
	}
	
	/**
	 * @return \mysqli
	 */
	private function getLinkByKey($key) {
		return isset($this->links[$key]) ? $this->links[$key] : null;
	}
	
	private function getQueryByKey($key) {
		return isset($this->queries[$key]) ? $this->queries[$key] : null;
	}
	
	private function getErrorByKey($key) {
		$link = $this->getLinkByKey($key);
		return $link->error;
	}
	
	private function initAccess($key) {
		HTimer::init('DB Async');
		$link = $this->getLinkByKey($key);
		if (!$link) HServer::sendServerError("Invalid key ".$key);
		return $link->reap_async_query();
	}
	
	protected function endAccess($res, $nrows, $key) {
		$query = $this->getQueryByKey($key);
		if ($res === true) {
			HTimer::end('DB Async', $nrows.' affected rows : '.$query);
		}
		elseif (!$res) {
			$msg = 'Error DB '.$this->getErrorByKey($key).' : '.$query;
			if ($this->db->isStrict()) \JNMFW\helpers\HServer::sendServerError($msg);
			else HLog::error($msg);
		}
		else {
			HTimer::end('DB Async', $nrows.' rows : '.$query);
			$res->free();
		}
		$this->freeQueryByKey($key);
	}
}
