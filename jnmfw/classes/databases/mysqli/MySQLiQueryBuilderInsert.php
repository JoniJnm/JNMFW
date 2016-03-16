<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\queryBuilder\DBBlockInserter;
use JNMFW\classes\databases\queryBuilder\DBQueryBuilderInsert;

class MySQLiQueryBuilderInsert extends MySQLiQueryBuilder implements DBQueryBuilderInsert
{
	private $values = array();
	private $onDuplicate = array();

	public function columns($columns)
	{
		return parent::columns($columns);
	}

	public function data($row)
	{
		$isArray = is_array($row);
		if (!$this->cols) {
			$this->cols = $isArray ? array_keys($row) : array_keys(get_object_vars($row));
		}
		$values = array();
		foreach ($this->cols as $col) {
			$value = $isArray ? $row[$col] : $row->$col;
			$values[] = $this->db->quote($value);
		}
		$this->values[] = '(' . implode(',', $values) . ')';
		return $this;
	}

	public function clearData()
	{
		$this->values = array();
	}

	public function getBlockInserter($blockSize)
	{
		return new DBBlockInserter($this, $blockSize);
	}

	public function onDuplicate($data)
	{
		foreach ($data as $key => $value) {
			$this->onDuplicate[] = $this->db->quoteName($key) . '=' . $this->db->quote($value);
		}
		return $this;
	}

	public function onDuplicateRaw($data)
	{
		foreach ($data as $key => $value) {
			$this->onDuplicate[] = $this->db->quoteName($key) . '=' . $value;
		}
		return $this;
	}

	public function onDuplicateUpdateColumns($columns)
	{
		foreach ($columns as $column) {
			$name = $this->db->quoteName($column);
			$this->onDuplicate[] = $name . '=VALUES(' . $name . ')';
		}
		return $this;
	}

	public function execute()
	{
		return parent::execute();
	}

	public function build()
	{
		$sql = 'INSERT INTO ' . $this->db->quoteName($this->table);
		$sql .= ' ' . $this->db->quoteNames($this->cols);
		$sql .= ' VALUES ' . implode(', ', $this->values);
		if ($this->onDuplicate) {
			$sql .= ' ON DUPLICATE KEY UPDATE ' . implode(', ', $this->onDuplicate);
		}
		return $sql;
	}
}
