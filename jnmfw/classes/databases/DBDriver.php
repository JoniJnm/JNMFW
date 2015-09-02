<?php

namespace JNMFW\classes\databases;

use JNMFW\classes\databases\DBAdapter;
use JNMFW\classes\databases\DBConnection;

abstract class DBDriver {
	/**
	 * Conector
	 * @param string $host El servidor de la base de datos (generalmente localhost)
	 * @param string $user El usuario para conexión
	 * @param string $pass La contraseña del usuario
	 * @param string $dbname El nombre de la base de datos donde conectarse (vacio para no conectarse a una db en concreto)
	 * @return DBAdapter Una instancia de conexión a MySQL
	 */
	abstract public function getAdapter($host, $user, $pass, $dbname='');
	
	/**
	 * @param DBAdapter $adapter
	 * @return DBConnection
	 */
	abstract public function getConnection(DBAdapter $adapter);
}
