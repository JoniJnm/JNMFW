<?php

namespace JNMFW\classes\databases;

use JNMFW\classes\databases\DBAdapter;
use JNMFW\classes\databases\DBConnection;

interface DBDriver {
	/**
	 * @return DBAdapter
	 */
	public function createAdapter();
	
	/**
	 * @return DBConnection
	 */
	public function createConnection();
}
