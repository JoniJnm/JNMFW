<?php

namespace JNMFW;

use JNMFW\classes\databases\DBConnection;

abstract class BaseObj {
	/**
	 * @var DBConnection
	 */
	protected $db;
	protected $item;
	
	public function __construct($item) {
		$this->db = \JNMFW\classes\databases\DBFactory::getInstance();
		$this->item = $item;
	}
}
