<?php

namespace JNMFW\classes\databases\queryBuilder;

class DBBlockInserter {
	/**
	 * @var DBQueryBuilderInsert 
	 */
	private $query;
	
	private $blockSize;
	
	private $currentSize = 0;
	
	public function __construct(DBQueryBuilderInsert $query, $blockSize) {
		$this->query = $query;
		$this->blockSize = $blockSize;
	}
	
	public function add($row) {
		$this->query->data($row);
		$this->currentSize++;
		if ($this->currentSize == $this->blockSize) {
			return $this->execute();
		}
		return 0;
	}
	
	public function end() {
		if ($this->currentSize > 0) {
			return $this->execute();
		}
		return 0;
	}
	
	private function execute() {
		$ret = $this->query->execute();
		$this->query->clearData();
		$this->currentSize = 0;
		return $ret;
	}
}
