<?php

namespace JNMFW\classes\databases\pdo;

use JNMFW\classes\databases\DBResource;

/**
 * Clase para adaptar un recurso devuelto por mysql_connect para ser usado como un recurso de MySQLi
 * Más información: http://www.php.net/manual/es/class.mysqli-result.php
 */

class PDOResource implements DBResource
{
	/**
	 * El recurso pdo
	 * @var \PDOStatement
	 */
	protected $res;

	private $freed = false;

	/**
	 * Constructor para instanciar esta clase y usarla como si fuera de tipo mysqli_result
	 * @param \PDOStatement $resource
	 */
	public function __construct(\PDOStatement $resource)
	{
		$this->res = $resource;
	}

	public function __destruct()
	{
		$this->free();
	}

	public function fetch_object($class_name = "stdClass")
	{
		return $this->res->fetchObject($class_name);
	}

	public function fetch_row()
	{
		return $this->res->fetch(\PDO::FETCH_ASSOC);
	}

	public function fetch_array()
	{
		return $this->res->fetch(\PDO::FETCH_NUM);
	}

	public function fetch_value($column_number = 0)
	{
		return $this->res->fetchColumn($column_number);
	}

	public function getNumRows()
	{
		return $this->res->rowCount();
	}

	public function free()
	{
		if (!$this->freed) {
			$this->freed = true;
			$this->res->closeCursor();
		}
	}
}