<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\DBDriver;
use JNMFW\helpers\HLog;

class MySQLiDriver extends DBDriver {
	protected $host;
	protected $user;
	protected $pass;
	protected $dbname;
	protected $port;
	
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
			HLog::error('Error de Conexión MySQLi '.$adapter->getError());
			return null;
		}
		else {
			HLog::verbose('Connected to MySQL DB with user '.$this->user);
			return $adapter;
		}
	}
	
	public function createConnection() {
		$adapter = $this->createAdapter();
		if (!$adapter) return null;
		return new MySQLiConnection($adapter, $this->getPrefix(), $this->isStrict());
	}
}
