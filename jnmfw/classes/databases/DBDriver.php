<?php

namespace JNMFW\classes\databases;

use JNMFW\classes\databases\DBAdapter;
use JNMFW\classes\databases\DBConnection;

abstract class DBDriver {
	private $prefix = null;
	
	
	/**
	 * @return DBAdapter
	 */
	abstract public function createAdapter();
	
	/**
	 * @return DBConnection
	 */
	abstract public function createConnection();
	
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}
	
	public function getPrefix() {
		return $this->prefix;
	}
}
