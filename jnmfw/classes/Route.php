<?php

namespace JNMFW\classes;

use JNMFW\classes\Request;

class Route {
	/**
	 * @var Request 
	 */
	private $request;
	
	private $path;
	private $method;
	private $funcs = array();
	
	public function __construct($path) {
		$this->request = Request::getInstance();
		$this->path = $path;
		$this->method = strtolower(filter_input(INPUT_SERVER, 'REQUEST_METHOD'));
	}
	
	public function getParts() {
		return $this->getPathParts($this->path);
	}
	
	/**
	 * @return Route
	 */
	public function get($path, $func = null) {
		return $this->addTask('get', $path, $func);
	}
	
	/**
	 * @return Route
	 */
	public function post($path, $func = null) {
		return $this->addTask('post', $path, $func);
	}
	
	/**
	 * @return Route
	 */
	public function put($path, $func = null) {
		return $this->addTask('put', $path, $func);
	}
	
	/**
	 * @return Route
	 */
	public function delete($path, $func = null) {
		return $this->addTask('delete', $path, $func);
	}
	
	/**
	 * @return Route
	 */
	public function allways($path, $func = null) {
		return $this->addTask('allways', $path, $func);
	}
	
	/**
	 * @return Route
	 */
	public function addDefaults() {
		return $this
			->get('/', 'fetchAll')
			->get('/:id', 'fetch')
			->post('/', 'create')
			->put('/:id', 'update')
			->put('/', 'update')
			->delete('/:id', 'destroy');
	}
	
	public function run($controller) {
		if ($this->funcs) {
			foreach ($this->funcs as $func) {
				$call = array($controller, $func);
				if (is_callable($call)) {
					call_user_func($call);
				}
			}
		}
	}
	
	private function addTask($method, $path, $func) {
		if ($this->method === $method || $this->method === 'allways') {
			$match = null;
			$path = $this->fixPath($path);
			$pattern = '/'.str_replace(array("-"), array("\\-"), $path);
			$pattern = preg_replace('#/:([^/]+)#', '/(?<$1>[^/]+)', $pattern);
			$pattern = '#^'.$pattern.'$#';
			
			if (preg_match($pattern, $this->path, $match)) {
				foreach ($match as $key => $value) {
					$this->request->addData($key, $value);
				}
				if (!$func) {
					$parts = $this->getPathParts($path);
					if ($parts && preg_match('/[A-Z0-9_]+/i', $parts[0])) {
						$func = $parts[0];
					}
				}
				if ($func) {
					$this->funcs[] = $func;
				}
			}
		}
		return $this;
	}
	
	private function getPathParts($path) {
		$path = $this->fixPath($path);
		if (!$path) return array();
		return explode('/', $path);
	}
	
	private function fixPath($path) {
		return preg_replace('#/+#', '/', trim($path, '/'));
	}
}
