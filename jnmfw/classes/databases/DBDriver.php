<?php

namespace JNMFW\classes\databases;

use JNMFW\classes\databases\DBAdapter;
use JNMFW\classes\databases\DBConnection;

abstract class DBDriver {
	/**
	 * @return DBAdapter Una instancia de conexión a la base de datos
	 */
	//abstract public function getAdapter($params = array());
	
	/**
	 * @param DBAdapter $adapter
	 * @return DBConnection
	 */
	abstract public function getConnection(DBAdapter $adapter);
}
