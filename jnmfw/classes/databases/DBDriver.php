<?php

namespace JNMFW\classes\databases;

use JNMFW\classes\databases\DBAdapter;
use JNMFW\classes\databases\DBConnection;

abstract class DBDriver {
	private $prefix;
	private $strict;
	
	/**
	 * @return DBAdapter
	 */
	abstract public function createAdapter();
	
	/**
	 * @return DBConnection
	 */
	abstract public function createConnection();
	
	protected function onCreateConnection(DBConnection $db) {
		$db->setPrefix($this->getPrefix());
		$db->setStrict($this->isStrict());
	}
	
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}
	
	public function getPrefix() {
		return $this->prefix;
	}
	
	public function setStrict($strict) {
		$this->strict = boolval($strict);
	}
	
	public function isStrict() {
		return $this->strict;
	}
}
