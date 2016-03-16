<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\exceptions\JNMDBException;
use JNMFW\helpers\HTimer;

class MySQLiPoll implements \JNMFW\classes\databases\DBPoll
{
	/**
	 * @var MySQLiConnection
	 */
	private $db;

	/**
	 * @var \mysqli[]
	 */
	private $links;

	/**
	 * @var string[]
	 */
	private $queries;

	public function __construct(MySQLiConnection $db, $queries)
	{
		if (!self::isAvaiable()) {
			throw new \RuntimeException("Can't make async query, mysqlnd extension is not installed");
		}

		$this->db = $db;
		$this->queries = $queries;
		foreach ($queries as $key => $query) {
			$link = $this->db->getDriver()->createNativeConnection();
			if (!$link->query($query, MYSQLI_ASYNC)) {
				$msg = $link->error . ' (' . $link->errno . ')';
				throw new JNMDBException($msg);
			}
			$this->links[$key] = $link;
		}
	}

	static public function isAvaiable()
	{
		return defined('MYSQLI_ASYNC') && extension_loaded('mysqlnd');
	}

	public function wait()
	{
		HTimer::init('DB Async wait');
		$read = $error = $reject = array();
		do {
			$read = $error = $reject = $this->links;
			mysqli_poll($read, $error, $reject, 1);
			$done = count($this->links) !== count($read) + count($error) + count($reject);
			if ($done) {
				break;
			}
			usleep(50);
		} while (true);
		HTimer::end('DB Async wait');
	}

	public function free()
	{
		foreach ($this->links as $link) {
			$link->close();
		}
		$this->links = array();
		$this->queries = array();
	}

	public function __destruct()
	{
		$this->free();
	}

	public function execute($key)
	{
		$res = $this->initAccess($key);
		$this->endAccess($res, $this->getAffectedRows(), $key);
		return $this->getAffectedRows();
	}

	public function loadObject($key, $class_name = "stdClass")
	{
		$res = $this->initAccess($key);
		$obj = $this->db->parseObject($res, $class_name);
		$this->endAccess($res, $obj ? 1 : 0, $key);
		return $obj;
	}

	public function loadObjectList($key, $class_name = "stdClass", $keycol = null)
	{
		$res = $this->initAccess($key);
		$array = $this->db->parseObjectList($res, $class_name, $keycol);
		$this->endAccess($res, count($array), $key);
		return $array;
	}

	public function loadValue($key, $col = 0)
	{
		$res = $this->initAccess($key);
		$value = $this->db->parseValue($res, $col);
		$this->endAccess($res, $value === false ? 0 : 1, $key);
		return $value;
	}

	public function loadValueArray($key, $col = 0)
	{
		$res = $this->initAccess($key);
		$values = $this->db->parseValueArray($res, $col);
		$this->endAccess($res, count($values), $key);
		return $values;
	}

	private function freeQueryByKey($key)
	{
		$link = $this->getLinkByKey($key);
		if (!$link) {
			throw new \InvalidArgumentException("Invalid key " . $key);
		}
		$link->close();
		unset($this->links[$key]);
		unset($this->queries[$key]);
	}

	/**
	 * @return \mysqli
	 */
	private function getLinkByKey($key)
	{
		return isset($this->links[$key]) ? $this->links[$key] : null;
	}

	private function getQueryByKey($key)
	{
		return isset($this->queries[$key]) ? $this->queries[$key] : null;
	}

	private function initAccess($key)
	{
		HTimer::init('DB Async');
		$link = $this->getLinkByKey($key);
		if (!$link) {
			throw new \InvalidArgumentException("Invalid key " . $key);
		}
		$res = $link->reap_async_query();
		if (!$res) {
			$query = $this->getQueryByKey($key);
			throw new JNMDBException($link->error . ":\n" . $query, $link->errno);
		}
		return $res;
	}

	protected function endAccess($res, $nrows, $key)
	{
		$query = $this->getQueryByKey($key);
		if ($res === true) {
			HTimer::end('DB Async', $nrows . ' affected rows : ' . $query);
		} elseif (!$res) {
			throw new JNMDBException("Unkown DB Error:\n" . $query);
		} else {
			HTimer::end('DB Async', $nrows . ' rows : ' . $query);
			$res->free();
		}
		$this->freeQueryByKey($key);
	}
}
