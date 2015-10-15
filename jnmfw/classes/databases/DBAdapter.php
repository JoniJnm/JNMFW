<?php

namespace JNMFW\classes\databases;

interface DBAdapter {	
	public function query($query);
	public function quote($value);
	public function getAffectedRows();
	public function getInsertedID();
	public function getError();
	public function transactionBegin();
	public function commit();
	public function rollback();
}