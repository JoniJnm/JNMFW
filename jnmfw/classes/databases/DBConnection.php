<?php

namespace JNMFW\classes\databases;

use JNMFW\helpers\HLog;
use JNMFW\helpers\HTimer;
use JNMFW\exceptions\JNMDBException;

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
	 * @var boolean
	 */
	private $intransaction = false;
	
	/**
	 * @var DBDriver 
	 */
	protected $driver = null;
	
	/**
	 * Constructor
	 * @param DBAdapter $conn La conexión devuelta por connect()
	 */
	public function __construct(DBAdapter $adapter, $prefix = null, $driver = null) {
		$this->conn = $adapter;
		$this->prefix = $prefix;
		$this->driver = $driver;
	}
	
	/**
	 * @return queryBuilder\DBQueryBuilderInsert
	 */
	abstract public function getQueryBuilderInsert($table);
	/**
	 * @return queryBuilder\DBQueryBuilderSelect
	 */
	abstract public function getQueryBuilderSelect($table, $alias='');
	/**
	 * @return queryBuilder\DBQueryBuilderUpdate
	 */
	abstract public function getQueryBuilderUpdate($table);
	/**
	 * @return queryBuilder\DBQueryBuilderDelete
	 */
	abstract public function getQueryBuilderDelete($table);
	/**
	 * @return queryBuilder\DBCondition
	 */
	abstract public function createConditionAnds();
	/**
	 * @return queryBuilder\DBCondition
	 */
	abstract public function createConditionOrs();
	/**
	 * @return DBPoll
	 */
	abstract public function getAsyncPoll($queries);
	
	/**
	 * Prepara una variable para ser insertada con seguridad.
	 * @param mixed $value
	 * @return string
	 */
	public function quote($value) {
		return $this->conn->quote($value);
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
		/*$parts = explode('.', $value); //hacer quote a table.column por separado
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
	 * Ejecuta una instrucción SQL (para insert, update o delete)
	 * @param string $query La instrucción SQL
	 * @return boolean Devuelve el número de filas afectadas, -1 en caso de error
	 */
	public function execute($query) {
		$res = $this->initAccess($query);
		$nrows = $this->getAffectedRows();
		$this->endAccess($res, $nrows);
		return $nrows;
	}

	/**
	 * Devuelte la primera fila de la consulta como objeto. Null si hubo error. Objeto vacío si no hubo resultados
	 * @param string $query La consulta SQL
	 * @return Un objeto stdclass con los valores devueltos por MySQL
	 */
	public function loadObject($query){
		$res = $this->initAccess($query);
		$obj = $this->parseObject($res);
		$this->endAccess($res, $obj ? 1 : 0);
		return $obj;
	}
	
	/**
	 * @param DBResource $res
	 */
	public function parseObject($res) {
		if (!$res) return false;
		return $res->fetch_object();
	}
	
	/**
	 * Devuelte un array de objetos, que serán cada fila de respuesta. Null en caso de error. Array vacío si no hubo resultados
	 * @param string $query La consulta SQL
	 * @param string $keycol Columna para crear los índices del array
	 * @return array de objetos stdclass
	 */
	public function loadObjectList($query, $keycol = null) {
		$res = $this->initAccess($query);
		$array = $this->parseObjectList($res, $keycol);
		$this->endAccess($res, count($array));
		return $array;
	}
	
	/**
	 * @param DBResource $res
	 */
	public function parseObjectList($res, $keycol) {
		if (!$res) return false;
		$array = array();
		while ($row = $res->fetch_object()) {
			if ($keycol) $array[$row->$keycol] = $row;
			else $array[] = $row;
		}
		return $array;
	}

	/**
	 * Devuelte el valor de la primera fila y columna a elegir de la consulta. False en caso de error o no resultados encontrados
	 * @param string $query La consulta SQL
	 * @param int $col El número de columna a obtener (por defecto la primera, es decir número 0)
	 * @return El valor de la primera fila y columna
	 */
	public function loadValue($query, $col = 0) {
		$res = $this->initAccess($query);
		$value = $this->parseValue($res, $col);
		$this->endAccess($res, $value === false ? 0 : 1);
		return $value;
	}
	
	/**
	 * @param DBResource $res
	 */
	public function parseValue($res, $col) {
		if (!$res) return false;
		return $res->fetch_value($col);
	}
	
	/**
	 * Devuelve los valores de la primera (u otra) columa de cada fila, como un array. 
	 * Null en caso de error. Array vacío si no hubo resultados
	 * @param string $query La consulta MySQL
	 * @param int $col El número de columna a obtener (por defecto la primera, es decir número 0)
	 * @return array de valores
	 */
	public function loadValueArray($query, $col = 0) {
		$res = $this->initAccess($query);
		$values = $this->parseValueArray($res, $col);
		$this->endAccess($res, count($values));
		return $values;
	}
	
	/**
	 * @param DBResource $res
	 */
	public function &parseValueArray($res, $col) {
		$array = null;
		if ($res) {
			$array = array();
			while ($row = $res->fetch_row()) {
				$array[] = $row[$col];
			}
		}
		return $array;
	}
	
	/**
	 * Returns DBResource to loop it manually
	 * @param string query
	 * @return DBResource
	 */
	public function loadResource($query) {
		return $this->query($query);
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
	 * Inicio de una transacción
	 */
	public function transactionBegin() {
		HLog::verbose("DB Transaction BEGIN");
		$this->conn->transactionBegin();
		$this->intransaction = true;
	}
	
	/**
	 * Commit de una transacción
	 */
	public function transactionCommit() {
		HLog::verbose("DB Transaction COMMIT");
		$this->conn->commit();
		$this->intransaction = false;
	}
	
	/**
	 * Rollback de una transacción
	 */
	public function transactionRollback() {
		HLog::verbose("DB Transaction ROLLBACK");
		$this->conn->rollback();
		$this->intransaction = false;
	}
	
	public function inTransaction() {
		return $this->intransaction;
	}
	
	public function close() {
		return $this->conn->close();
	}
	
	public function getDriver() {
		if ($this->driver === null) {
			throw new JNMDBException('Connection created without driver');
		}
		return $this->driver;
	}
	
	/**
	 * Inicia el acceso
	 */
	protected function initAccess($query) {
		HTimer::init('DB');
		return $this->query($query);
	}
	
	/**
	 * Finaliza el acceso
	 * @param DBResource $res Resultado
	 */
	protected function endAccess($res, $nrows) {
		if ($res === true) {
			HTimer::end('DB', $nrows.' affected rows : '.$this->query);
		}
		elseif (!$res) {
			$msg = "Unkown DB Error in query:\n".$this->query;
			throw new JNMDBException($msg);
		}
		else {
			HTimer::end('DB', $nrows.' rows : '.$this->query);
			$res->free();
		}
	}
	
	/**
	 * Consulta
	 * @param string|queryBuilder\DBQueryBuilder $query La consulta SQL
	 * @return DBResource resultado de la consulta
	 */
	protected function query($query) {
		if ($query instanceof queryBuilder\DBQueryBuilder) $this->query = $query->build();
		else $this->query = $query;
		$out = $this->conn->query($query);
		return $out;
	}
}