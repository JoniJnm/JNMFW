<?php

namespace JNMFW\classes\databases\pdo;

use JNMFW\classes\databases\DBDriver;
use JNMFW\helpers\HLog;

class PDODriver extends DBDriver {
	private $dns;
	private $user;
	private $pass;
	private $options;
	
	public function __construct($dsn, $user, $pass, $options = array()) {
		$this->dns = $dsn;
		$this->user = $user;
		$this->pass = $pass;
		$this->options = $options;
	}
	
	public function createAdapter() {
		$adapter = new PDOAdapter($this->dsn, $this->user, $this->pass, $this->options);
		if ($adapter->getError()) {
			HLog::error('Error de Conexión PDO '.$adapter->getError());
			return null;
		}
		else {
			HLog::verbose('Connected to PDO DB with user '.$this->user);
			return $adapter;
		}
	}
	
	public function createConnection() {
		$adapter = $this->createAdapter();
		if (!$adapter) return null;
		return new PDOConnection($adapter, $this->getPrefix());
	}
}
