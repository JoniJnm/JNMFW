<?php

namespace JNMFW;

abstract class BaseObj {
	/**
	 * @var \JNMFW\classes\databases\Database
	 */
	protected $db;
	protected $item;
	
	public function __construct($item) {
		$this->db = \JNMFW\classes\databases\DBFactory::getInstance();
		$this->item = $item;
	}
}
