<?php

namespace JNMFW\classes\databases;

use JNMFW\exceptions\JNMDBException;

abstract class DBDriver
{
	private $prefix = null;

	/**
	 * @return DBAdapter
	 */
	abstract public function createAdapter();

	/**
	 * @return DBConnection
	 */
	abstract public function createConnection();

	/**
	 * @throws JNMDBException
	 */
	abstract public function createNativeConnection();

	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}

	public function getPrefix() {
		return $this->prefix;
	}
}
