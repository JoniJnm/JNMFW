<?php

namespace JNMFW;

use JNMFW\classes\databases\DBConnection;
use JNMFW\TableBase;

abstract class ObjBase {
	/**
	 * @var DBConnection
	 */
	protected $db;
	protected $item;
	
	public function __construct($item) {
		$this->db = \JNMFW\classes\databases\DBFactory::getInstance();
		$this->item = $item;
	}
	
	/**
	 * @return TableBase
	 */
	public function getItem() {
		return $this->item;
	}
}

