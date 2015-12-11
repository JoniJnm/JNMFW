<?php

namespace JNMFW;

abstract class ModelBase extends ModelSimple {
	private $objs = array();
	
	protected function getByPrimaryKey($id, $tableName, $objName) {
		if (!isset($this->objs[$objName])) {
			$this->objs[$objName] = array();
		}
		$dirty = !isset($this->objs[$objName][$id]) || $this->objs[$objName][$id]->getItem()->isDirty();
		if ($dirty) {
			$item = $tableName::get($id);
			if (!$item) return null;
			$this->objs[$objName][$id] = new $objName($item);
		}
		return $this->objs[$objName][$id];
	}
	
	protected function getMultiByPrimaryKey($ids, $tableName, $objName) {
		if (!isset($this->objs[$objName])) {
			$this->objs[$objName] = array();
		}
		$out = array();
		$dirtys = array();
		foreach ($ids as $id) {
			$dirty = !isset($this->objs[$objName][$id]) || $this->objs[$objName][$id]->getItem()->isDirty();
			if ($dirty) {
				$dirtys[] = $id;
			}
			else {
				$out[] = $this->objs[$objName][$id];
			}
		}
		
		if ($dirtys) {
			$items = $tableName::getMulti($dirtys);
			foreach ($items as $item) {
				if (!$item) continue;
				$obj = new $objName($item);
				$out[] = $obj;
				$pk = $item->getPrimaryKey();
				$id = $item->$pk;
				$this->objs[$objName][$id] = $obj;
			}
		}
		
		return $out;
	}
}
