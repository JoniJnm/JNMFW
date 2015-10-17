<?php

namespace JNMFW\classes\databases\pdo;

use JNMFW\classes\databases\DBDriver;

class PDODriver implements DBDriver {
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
			\JNMFW\helpers\HLog::error('Error de Conexión PDO '.$adapter->getError());
			return null;
		}
		else {
			return $adapter;
		}
	}
	
	public function createConnection() {
		$adapter = $this->createAdapter();
		if ($adapter)
			return new PDOConnection($adapter);
		else
			return null;
	}
}
