<?php

namespace JNMFW\classes\databases;

use JNMFW\helpers\HLog;

abstract class DBConnection {
	/**
	 * @var DBAdapter
	 */
	protected $conn;
	
	/**
	 * @var string
	 */
	protected $query;
	
	/**
	 * @var string
	 */
	protected $prefix = null;
	
	/**
	 * @var int
	 */
	private $num_rows = 0;
	
	/**
	 * @var boolean
	 */
	private $strict = true;
	
	/**
	 * Constructor
	 * @param DBAdapter $conn La conexión devuelta por connect()
	 */
	public function __construct(DBAdapter $conn) {
		$this->conn = $conn;
	}
	
	/**
	 * @return \JNMFW\classes\databases\queryBuilder\DBQueryBuilderInsert
	 */
	abstract public function getQueryBuilderInsert($table);
	/**
	 * @return \JNMFW\classes\databases\queryBuilder\DBQueryBuilderSelect
	 */
	abstract public function getQueryBuilderSelect($table, $alias='');
	/**
	 * @return \JNMFW\classes\databases\queryBuilder\DBQueryBuilderUpdate
	 */
	abstract public function getQueryBuilderUpdate($table);
	/**
	 * @return \JNMFW\classes\databases\queryBuilder\DBQueryBuilderDelete
	 */
	abstract public function getQueryBuilderDelete($table);
	
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}
	
	public function setStrict($strict) {
		$this->strict = $strict;
	}
	
	/**
	 * Prepara una variable para ser insertada con seguridad.
	 * @param string $val
	 * @return string
	 */
	public function quote($val) {
		if (is_null($val)) return 'NULL';
		//elseif (\is_numeric($val)) return $val;
		else return "'".$this->conn->escape($val)."'";
	}
	
	/**
	 * Prepara un array para ser insertado con seguridad en una consulta 
	 * del tipo INSERT VALUES o SELECT IN
	 * @param mixed[] $values
	 */
	public function quoteArray($values) {
		$arr = array();
		foreach ($values as $value) {
			$arr[] = $this->quote($value);
		}
		return '('.\implode(',', $arr).')';
	}
	
	/**
	 * Prepara un identificador (columnas, tablas, nombre de base de datos...)
	 * para ser insertado con seguridad.
	 * @param string $value
	 */
	public function quoteName($value) {
		if ($this->prefix && substr($value, 0, 3) == '#__') {
			$value = str_replace('#__', $this->prefix, $value);
		}
		if (!preg_match('/^[A-Z0-9_\.-]+$/i', $value)) {
			return $value;
		}
		if (strpos($value, '.') === false) return '`'.$value.'`'; //si no tiene partes...
		/*$parts = explode('.', $value); //hacer quote a table.colum por separado
		return '`'.implode('`.`', $parts).'`';*/
		return $value;
	}
	
	/**
	 * Prepara un array de indetificadores para ser insertador
	 * con seguridad
	 * @param string $values
	 */
	public function quoteNames($values, $complete=true) {
		$arr = array();
		foreach ($values as $value) {
			$arr[] = $this->quoteName($value);
		}
		if ($complete) return '('.\implode(',', $arr).')';
		else return \implode(', ', $arr);
	}
	
	/**
	 * Prepara una fecha para ser insertada en la db
	 * @param string $date En cualquier formato válido: http://www.php.net/manual/es/datetime.formats.php
	 * return string devuelve la fecha y hora en formato YYYY-mm-dd
	 */
	public function formatDate($date) {
		return $this->format_date($date, 'Y-m-d');
	}
	
	/**
	 * Prepara una fecha para ser insertada en la db
	 * @param string $date En cualquier formato válido: http://www.php.net/manual/es/datetime.formats.php
	 * return string devuelve la fecha y hora en formato yyyy-mm-dd hh:mm:ss
	 */
	public function formatDateTime($date) {
		return $this->format_date($date, 'Y-m-d H:i:s');
	}
	
	private function format_date($date, $formato) {
		if (\is_numeric($date)) {
			$fech = new \DateTime(null);
			$fech->setTimestamp($date);
		}
		else {
			$fech = new \DateTime($date);	
		}
		$fech->setTimezone(static::getDateTimeZoneUTC());
		return $fech->format($formato);
	}
	
	private static function getDateTimeZoneUTC() {
		static $timezone = null;
		if ($timezone === null) $timezone = new \DateTimeZone("UTC");
		return $timezone;
	}
	
	public function getNullDate() {
		return '0000-00-00';
	}
	
	public function getNullDateTime() {
		return '0000-00-00 00:00:00';
	}
	
	public function isNullDate($date) {
		return empty($date) || $date == $this->getNullDate() || $date == $this->getNullDateTime();
	}

	/**
	 * Ejecuta una instrucción SQL (para insert, update o delete)
	 * @param string $query La instrucción SQL
	 * @return int Devuelve el número de filas afectadas, -1 en caso de error
	 */
	public function execute($query) {
		$this->iniciarAcceso();
		$res = $this->query($query);
		$this->finalizarAcceso($res);
		return $this->getAffectedRows();
	}

	/**
	 * Devuelte la primera fila de la consulta como objeto. Null si hubo error. Objeto vacío si no hubo resultados
	 * @param string $query La consulta SQL
	 * @return Un objeto stdclass con los valores devueltos por MySQL
	 */
	public function loadObject($query){
		$this->iniciarAcceso();
		$res = $this->query($query);
		$obj = null;
		if ($res) {
			if ($object = $res->fetch_object()) {
				$obj = $object;
			}
		}
		$this->finalizarAcceso($res);
		return $obj;
	}
	
	/**
	 * Devuelte un array de objetos, que serán cada fila de respuesta. Null en caso de error. Array vacío si no hubo resultados
	 * @param string $query La consulta SQL
	 * @param string $keycol Columna para crear los índices del array
	 * @return array de objetos stdclass
	 */
	public function loadObjectList($query, $keycol = null) {
		$this->iniciarAcceso();
		$res = $this->query($query);
		$array = null;
		if ($res) {
			$array = array();
			while ($row = $res->fetch_object()) {
				if ($keycol) $array[$row->$keycol] = $row;
				else $array[] = $row;
			}
		}
		$this->finalizarAcceso($res);
		return $array;
	}

	/**
	 * Devuelte el valor de la primera fila y columna a elegir de la consulta. False en caso de error o no resultados encontrados
	 * @param string $query La consulta SQL
	 * @param int $col El número de columna a obtener (por defecto la primera, es decir número 0)
	 * @return El valor de la primera fila y columna
	 */
	public function loadResult($query, $col = 0) {
		$this->iniciarAcceso();
		$res = $this->query($query);
		$value = false;
		if ($res) {
			if ($row = $res->fetch_row()) {
				$value = $row[$col];
			}
		}
		$this->finalizarAcceso($res);
		return $value;
	}
	
	/**
	 * Devuelve los valores de la primera (u otra) columa de cada fila, como un array. 
	 * Null en caso de error. Array vacío si no hubo resultados
	 * @param string $query La consulta MySQL
	 * @param int $col El número de columna a obtener (por defecto la primera, es decir número 0)
	 * @return array de valores
	 */
	public function &loadResultArray($query, $col = 0) {
		$this->iniciarAcceso();
		$res = $this->query($query);
		$array = null;
		if ($res) {
			$array = array();
			while ($row = $res->fetch_row()) {
				$array[] = $row[$col];
			}
		}
		$this->finalizarAcceso($res);
		return $array;
	}
	
	/**
	 * Devuelve el número de filas afectadas en la última operación MySQL
	 * @return int El número de filas afectadas
	 */
	public function getAffectedRows() {
		return $this->conn->getAffectedRows();
	}
	
	/**
	 * Devuelve el id de la última fila generada (para inser con auto_increment)
	 *
	 */
	public function getLastInsertedId() {
		return $this->conn->getInsertedID();
	}
	
	/**
	 * Obtiene el número de filas devueltas en la última operación MySQL
	 * @return int La cantidad de filas devueltas
	 */
	public function getNumRows() {
		return $this->num_rows;
	}
	
	/**
	 * Inicio de una transacción
	 */
	public function transactionBegin() {
		HLog::logVerbose("dbTransaction: BEGIN");
		$this->conn->transactionBegin();
	}
	
	/**
	 * Commit de una transacción
	 */
	public function transactionCommit() {
		HLog::logVerbose("dbTransaction: COMMIT");
		$this->conn->commit();
	}
	
	/**
	 * Rollback de una transacción
	 */
	public function transactionRollback() {
		HLog::logVerbose("dbTransaction: ROLLBACK");
		$this->conn->rollback();
	}
	
	public function getLastError() {
		return $this->conn->getError();
	}
	
	/**
	 * Inicia el acceso
	 */
	protected function iniciarAcceso() {
		/*global $numQueries;
		$numQueries++;
		inicializarTimer('accesoDB');*/
	}
	
	/**
	 * Finaliza el acceso
	 * @param DBResourceAdapter $res Resultado
	 */
	protected function finalizarAcceso($res) {
		if ($res === true) {
			$this->num_rows = 0;
			//finalizarTimer('accesoDB',  $this->getAffectedRows().' affected rows : '.$this->query);
		}
		elseif (!$res) {
			$this->num_rows = -1;
			$msg = "Error BD al ejecutar ".$this->query.": ".$this->conn->getError();
			if ($this->strict) \JNMFW\helpers\HServer::sendServerError($msg);
			else HLog::logDebug($msg);
		}
		else {
			$this->num_rows = $res->getNumRows();
			//finalizarTimer('accesoDB', $this->num_rows.' rows : '.$this->query . (!LOCALHOST ? '' : ' - ' . BlinkFW_LogFile::getBacktrace()));
			$res->free();
		}
	}
	
	/**
	 * Consulta
	 * @param string|queryBuilder\DBQueryBuilder $query La consulta SQL
	 * @return DBResourceAdapter resultado de la consulta
	 */
	protected function query($query) {
		if ($query instanceof queryBuilder\DBQueryBuilder) $this->query = $query->build();
		else $this->query = $query;
		$out = $this->conn->query($query);
		return $out;
	}
}