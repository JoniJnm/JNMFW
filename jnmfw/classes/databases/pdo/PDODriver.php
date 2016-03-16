<?php

namespace JNMFW\classes\databases\pdo;

use JNMFW\classes\databases\DBDriver;
use JNMFW\exceptions\JNMDBConnectionException;

class PDODriver extends DBDriver
{
	private $dsn;
	private $user;
	private $pass;
	private $options;

	public function __construct($dsn, $user, $pass, $options = array())
	{
		$this->dsn = $dsn;
		$this->user = $user;
		$this->pass = $pass;
		$this->options = $options;
	}

	public function createNativeConnection()
	{
		$conn = new \PDO($this->dsn, $this->user, $this->pass, $this->options);
		if ($conn->errorCode()) {
			throw new JNMDBConnectionException($conn->errorInfo(), $conn->errorCode());
		}
		return $conn;
	}

	public function createAdapter()
	{
		$nativeConnection = $this->createNativeConnection();
		return new PDOAdapter($nativeConnection);
	}

	public function createConnection()
	{
		$adapter = $this->createAdapter();
		return new PDOConnection($adapter, $this->getPrefix(), $this);
	}
}
