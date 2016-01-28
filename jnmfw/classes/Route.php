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
	private $func = null;
	
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
	
	public function post($path, $func = null) {
		return $this->addTask('post', $path, $func);
	}
	
	public function put($path, $func = null) {
		return $this->addTask('put', $path, $func);
	}
	
	public function run($controller) {
		if (!$this->func) {
			//defaults methods
			$this->get('/', 'get');
			$this->post('/', 'create');
			$this->put('/', 'update');
		}
		
		if ($this->func) {
			$call = array($controller, $this->func);
			if (is_callable($call)) {
				call_user_func($call);
			}
		}
	}
	
	private function addTask($method, $path, $func) {
		if (!$this->func && $this->method === $method) {
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
					if ($parts) {
						$func = $parts[0];
					}
				}
				$this->func = $func;
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
