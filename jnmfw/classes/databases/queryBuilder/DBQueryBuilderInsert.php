<?php

namespace JNMFW\classes\databases\queryBuilder;

interface DBQueryBuilderInsert extends DBQueryBuilder
{
	/**
	 * @return DBQueryBuilderInsert
	 */
	public function addOption($option);

	/**
	 * @return DBQueryBuilderInsert
	 */
	public function columns($columns);

	/**
	 * Add one row to insert
	 * @param array|object $row the keys are the columns
	 * @return DBQueryBuilderInsert
	 */
	public function data($row);

	/**
	 * @return DBQueryBuilderInsert
	 */
	public function clearData();

	/**
	 * @return DBBlockInserter
	 */
	public function getBlockInserter($blockSize);

	/**
	 * @return DBQueryBuilderInsert
	 */
	public function onDuplicate($data);

	/**
	 * @return DBQueryBuilderInsert
	 */
	public function onDuplicateRaw($data);

	/**
	 * @return DBQueryBuilderInsert
	 */
	public function onDuplicateUpdateColumns($columns);

	/**
	 * Devuelve el número de filas afectadas, -1 en caso de error
	 */
	public function execute();
}
