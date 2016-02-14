<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\mysqli\MySQLiTypes;

/**
 * Clase para adaptar un recurso devuelto por mysql_connect para ser usado como un recurso de MySQLi
 * Más información: http://www.php.net/manual/class.mysqli-result.php
 */

class MySQLiResource implements \JNMFW\classes\databases\DBResource {
	/**
	 * El recurso mysql
	 * @var \mysqli_result
	 */
	protected $res;
	
	protected $freed = false;
	private $fields_col_number = array();
	private $fields_col_name = array();
	
	/**
	 * Constructor para instanciar esta clase y usarla como si fuera de tipo mysqli_result
	 * @param \mysqli_result $resource
	 */
	public function __construct($resource) {
		$this->res = $resource;
		$fields = $this->res->fetch_fields();
		$nfield = count($fields);
		for ($i=0; $i<$nfield; $i++) {
			$field = $fields[$i];
			$type = $field->type;
			if (isset(MySQLiTypes::$TYPES[$type])) {
				$name = $field->name;
				$this->fields_col_name[$name] = MySQLiTypes::$TYPES[$type];
				$this->fields_col_number[$i] = MySQLiTypes::$TYPES[$type];
			}
		}
	}
	
	public function __destruct() {
		$this->free();
	}
	
	public function fetch_object($class_name = "stdClass") {
		$obj = $this->res->fetch_object($class_name);
		if ($obj) $this->fixObjectTypes($obj);
		return $obj;
	}
	
	public function fetch_row() {
		$arr = $this->res->fetch_row();
		if ($arr) $this->fixArrayNumberTypes($arr);
		return $arr;
	}
	
	public function fetch_array() {
		$arr = $this->res->fetch_array(\MYSQLI_ASSOC);
		if ($arr) $this->fixArrayNameTypes($arr);
		return $arr;
	}
	
	public function fetch_value($column_number = 0) {
		$row = $this->fetch_row();
		return $row ? $row[$column_number] : null;
	}
	
	public function getNumRows() {
		return $this->res->num_rows;
	}
	
	private function fixObjectTypes(&$obj) {
		foreach ($this->fields_col_name as $name => $type) {
			if ($obj->$name === null) continue;
			settype($obj->$name, $type);
		}
	}
	
	private function fixArrayNameTypes(&$arr) {
		foreach ($this->fields_col_name as $name => $type) {
			if ($arr[$name] === null) continue;
			settype($arr[$name], $type);
		}
	}
	
	private function fixArrayNumberTypes(&$arr) {
		foreach ($this->fields_col_number as $i => $type) {
			if ($arr[$i] === null) continue;
			settype($arr[$i], $type);
		}
	}
	
	public function free() {
		if (!$this->freed) {
			$this->freed = true;
			$this->res->free();
		}
	}
}