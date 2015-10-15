<?php

namespace JNMFW\classes\databases;

interface DBPoll {
	public function loadObject($queryKey);
	public function loadObjectList($queryKey, $keycol = null);
	public function loadValue($queryKey, $col = 0);
	public function loadValueArray($queryKey, $col = 0);
	public function free();
	public function wait();
}
