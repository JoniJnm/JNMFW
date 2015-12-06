<?php

namespace JNMFW;

use JNMFW\classes\databases\DBConnection;

abstract class SimpleModel extends classes\Singleton {
	/**
	 * @var DBConnection
	 */
	protected $db;
	
	protected function __construct() {
		$this->db = \JNMFW\classes\databases\DBFactory::getInstance();
	}
}
