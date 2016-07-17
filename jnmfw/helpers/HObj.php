<?php

namespace JNMFW\helpers;

abstract class HObj {
	/**
	 * @param \stdClass[]|\stdClass $objs lista de objetos a completar
	 * @param callable|string $objKey obtener ids de los objetos
	 * @param callable $getDataFunc obtener datos usando los ids de los objetos
	 * @param callable|string $itemKey asociar salida de datos con objeto
	 * @param callable|string $setFunc setear dato con objeto
	 * @return array|\stdClass
	 */
	static public function completeObjects(array $objs, $objKey, $getDataFunc, $itemKey, $setFunc) {
		if (!is_array($objs)) {
			return self::completeObject($objs);
		}
		if (!count($objs)) {
			return $objs;
		}
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
		if (!$data) {
			return $objs;
		}
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
				$key = $obj->$objKey;
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

	static public function completeObject($obj, $objKey, $getDataFunc, $itemKey, $setFunc) {
		$objs = self::completeObjects(array($obj), $objKey, $getDataFunc, $itemKey, $setFunc);
		return $objs[0];
	}
}
