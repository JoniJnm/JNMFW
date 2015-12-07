<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\DBDriver;
use JNMFW\exceptions\JNMDBConnectionException;
use JNMFW\exceptions\JNMDBException;

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
	
	public function createNativeConnection() {
		$conn = new \mysqli($this->host, $this->user, $this->pass, $this->dbname, $this->port);
		if ($conn->connect_errno) {
			throw new JNMDBConnectionException($conn->connect_error.' ('.$conn->connect_errno.')');
		}
		else {
			$conn->set_charset("utf8");
			if ($conn->errno) {
				throw new JNMDBException($conn->error." (".$conn->connect_errno.")");
			}
		}
	}
	
	public function createAdapter() {
		$nativeConnection = $this->createNativeConnection();
		return new MySQLiAdapter($nativeConnection);
	}
	
	public function createConnection() {
		$adapter = $this->createAdapter();
		return new MySQLiConnection($adapter, $this->getPrefix(), $this);
	}
}
