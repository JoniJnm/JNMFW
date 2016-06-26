<?php

namespace JNMFW\helpers;

abstract class HObj {
	static public function completeObjects(array $objs, $objKey, $getDataFunc, $itemKey, $setFunc) {
		if (!is_string($objKey) && !is_callable($objKey)) {
			throw new \InvalidArgumentException('Invalid $objKey, must be string or function');
		}
		if (!is_string($itemKey) && !is_callable($itemKey)) {
			throw new \InvalidArgumentException('Invalid $itemKey, must be string or function');
		}
		$objKeys = array_map(function($obj) use($objKey) {
			if (is_string($objKey)) {
				return $obj->$objKey;
			}
			else {
				return $objKey($obj);
			}
		}, $objs);

		$data = $getDataFunc($objKeys);
		$items = array();
		foreach ($data as $item) {
			if (is_string($itemKey)) {
				$key = $item->$itemKey;
			}
			else {
				$key = $itemKey($item);
			}
			$items[$key] = $item;
		}
		$setMethod = null; //1:func direct, 2:func with object, 3:key
		foreach ($objs as $obj) {
			if (is_string($objKey)) {
				$key = $obj->$itemKey;
			}
			else {
				$key = $objKey($obj);
			}
			if ($setMethod === null) {
				if (is_callable($setFunc)) {
					$setMethod = 1;
				}
				elseif (is_callable(array($obj, $setFunc))) {
					$setMethod = 2;
				}
				else {
					$setMethod = 3;
				}
			}
			if (isset($items[$key])) {
				if ($setMethod == 1) {
					$setFunc($obj, $items[$key]);
				}
				elseif ($setMethod == 2) {
					call_user_func(array($obj, $setFunc), $items[$key]);
				}
				else {
					$obj->$setFunc = $items[$key];
				}
			}
		}
		return $objs;
	}
}
