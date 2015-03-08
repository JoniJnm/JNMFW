<?php

namespace JNMFW;

use JNMFW\classes\databases\DatabaseConnection;

abstract class BaseObj {
	/**
	 * @var DatabaseConnection
	 */
	protected $db;
	protected $item;
	
	public function __construct($item) {
		$this->db = \JNMFW\classes\databases\DBFactory::getInstance();
		$this->item = $item;
	}
}
