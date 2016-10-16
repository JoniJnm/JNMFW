<?php

namespace JNMFW\classes\databases;

interface DBAdapter
{
	public function __construct($nativeConnection);

	public function getNativeConnection();

	public function query($query);

	public function quote($value);

	public function getAffectedRows();

	public function getInsertedID();

	public function transactionBegin();

	public function commit();

	public function rollback();

	public function close();
}