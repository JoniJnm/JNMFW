<?php

namespace JNMFW;

use JNMFW\classes\databases\DBConnection;
use JNMFW\classes\databases\DBFactory;

abstract class ObjBase
{
	/**
	 * @var DBConnection
	 */
	protected $db;
	protected $item;

	public function __construct($item)
	{
		$this->db = DBFactory::getInstance();
		$this->item = $item;
	}

	/**
	 * @return TableBase
	 */
	public function getItem()
	{
		return $this->item;
	}
}

