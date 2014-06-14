<?php

namespace JNMFW\classes\cache;

interface ICache {
	public function exists($id);
	public function load($id);
	public function save($id, $data);
	public function delete($id);
}
