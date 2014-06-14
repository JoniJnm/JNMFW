<?php

namespace JNMFW\classes\databases;

interface DBAdapter {	
	/**
	 * Crea una instancia de esta clase para ser usada como objeto MySQLi
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 * @param string $dbname
	 */
	public function __construct($host, $user, $pass, $dbname='');
	
	public function query($query);
	public function escape($str);
	public function getAffectedRows();
	public function getInsertedID();
	public function getError();
	public function transaccionBegin();
	public function commit();
	public function rollback();
}