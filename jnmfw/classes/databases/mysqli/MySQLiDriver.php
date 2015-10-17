<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\DBDriver;

class MySQLiDriver implements DBDriver {
	private $host;
	private $user;
	private $pass;
	private $dbname;
	private $port;
	
	public function __construct($host, $user, $pass, $dbname='', $port=3306) {
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->dbname = $dbname;
		$this->port = $port;
	}
	
	public function createAdapter() {
		$adapter = new MySQLiAdapter($this->host, $this->user, $this->pass, $this->dbname);
		if ($adapter->getError()) {
			\JNMFW\helpers\HLog::error('Error de Conexión MySQLi '.$adapter->getError());
			return null;
		}
		else {
			return $adapter;
		}
	}
	
	public function createConnection() {
		$adapter = $this->createAdapter();
		if ($adapter)
			return new MySQLiConnection($adapter);
		else
			return null;
	}
}
