<?php

namespace JNMFW;

use JNMFW\classes\databases\DBConnection;
use JNMFW\classes\databases\DBFactory;

abstract class ServiceSimple extends classes\Singleton
{
	/**
	 * @var DBConnection
	 */
	protected $db;

	protected function __construct() {
		$this->db = DBFactory::getInstance();
	}
}
