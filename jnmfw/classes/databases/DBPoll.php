<?php

namespace JNMFW\classes\databases;

interface DBPoll {
	public function loadObject($queryKey, $class_name = "stdClass");
	public function loadObjectList($queryKey, $class_name = "stdClass", $keycol = null);
	public function loadValue($queryKey, $col = 0);
	public function loadValueArray($queryKey, $col = 0);
	public function free();
	public function wait();
}
