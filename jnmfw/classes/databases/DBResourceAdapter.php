<?php

namespace JNMFW\classes\databases;

/**
 * Clase para adaptar un recurso devuelto por mysql_connect para ser usado como un recurso de MySQLi
 * Más información: http://www.php.net/manual/es/class.mysqli-result.php
 *
 */

interface DBResourceAdapter {
	public function __construct($resource);
	public function fetch_object();
	public function fetch_row();
	public function fetch_array();
	public function getNumRows();
	public function free();
}