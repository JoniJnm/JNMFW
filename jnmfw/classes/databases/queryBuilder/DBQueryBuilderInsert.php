<?php

namespace JNMFW\classes\databases\queryBuilder;

interface DBQueryBuilderInsert extends DBQueryBuilder
{
	/**
	 * @return $this
	 */
	public function addOption($option);

	/**
	 * @return $this
	 */
	public function columns($columns);

	/**
	 * Add one row to insert
	 * @param array|object $row the keys are the columns
	 * @return $this
	 */
	public function data($row);

	/**
	 * @return $this
	 */
	public function clearData();

	/**
	 * @return $this
	 */
	public function getBlockInserter($blockSize);

	/**
	 * @return $this
	 */
	public function onDuplicate($data);

	/**
	 * @return $this
	 */
	public function onDuplicateRaw($data);

	/**
	 * @return $this
	 */
	public function onDuplicateUpdateColumns($columns);

	/**
	 * Devuelve el número de filas afectadas, -1 en caso de error
	 */
	public function execute();
}
