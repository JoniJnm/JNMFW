<?php

namespace JNMFW\classes\databases\mysqli;

use JNMFW\classes\databases\DBConnection;
use JNMFW\classes\databases\queryBuilder\DBCondition;

class MySQLiCondition implements DBCondition
{
	/**
	 * @var DBConnection
	 */
	private $db;

	private $wheres = array();
	private $glue = 'AND';

	public function __construct(DBConnection $db)
	{
		$this->db = $db;
	}

	public function setGlueAnd()
	{
		$this->glue = 'AND';
		return $this;
	}

	public function setGlueOr()
	{
		$this->glue = 'OR';
		return $this;
	}

	public function where($column, $value, $op = '=')
	{
		$this->wheres[] = $this->db->quoteName($column) . ' ' . $op . ' ' . $this->db->quote($value);
		return $this;
	}

	public function whereCondition(DBCondition $condition)
	{
		if (!$condition->isEmpty()) {
			$this->wheres[] = '(' . $condition->build() . ')';
		}
		return $this;
	}

	public function whereNull($column)
	{
		return $this->where($column, null, 'IS');
	}

	public function whereNotNull($column)
	{
		return $this->where($column, null, 'IS NOT');
	}

	public function whereColumns($col1, $col2, $op = '=')
	{
		$this->wheres[] = $this->db->quoteName($col1) . ' ' . $op . ' ' . $this->db->quoteName($col2);
		return $this;
	}

	public function whereLike($column, $value)
	{
		return $this->where($column, $value, 'LIKE');
	}

	public function whereNotLike($column, $value)
	{
		return $this->where($column, $value, 'NOT LIKE');
	}

	public function whereIn($column, $values)
	{
		$this->wheres[] = $this->db->quoteName($column) . ' IN ' . $this->db->quoteArray($values);
		return $this;
	}

	public function whereNotIn($column, $values)
	{
		$this->wheres[] = $this->db->quoteName($column) . ' NOT IN ' . $this->db->quoteArray($values);
		return $this;
	}

	public function whereRaw($condition, $data = null)
	{
		if (!preg_match('/\s*\(/', $condition)) {
			$condition = '(' . $condition . ')';
		}
		if ($data) {
			$from = array();
			$to = array();
			foreach ($data as $key => $value) {
				$from[] = '{' . $key . '}';
				$to[] = $this->db->quote($value);
			}
			$this->wheres[] = str_replace($from, $to, $condition);
		} else {
			$this->wheres[] = $condition;
		}
		return $this;
	}

	public function isEmpty()
	{
		return !$this->wheres;
	}

	public function build()
	{
		if ($this->isEmpty()) {
			return '';
		}
		return implode(' ' . $this->glue . ' ', $this->wheres);
	}
}
