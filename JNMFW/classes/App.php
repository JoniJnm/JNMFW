<?php

namespace JNMFW\classes;

use JNMFW\exceptions\JNMException;
use JNMFW\helpers\HServer;

class App
{
	private $paths = array();
	private $redirects = array();
	private $request;

	public function __construct() {
		$this->request = Request::getInstance();
	}

	/**
	 * @return App
	 */
	public function controllers($path, $namepsace) {
		$parts = $this->getPathParts($path);
		$length = count($parts);
		if (!isset($this->paths[$length])) {
			$this->paths[$length] = array();
			krsort($this->paths, SORT_NUMERIC);
		}

		$this->paths[$length] = array(
			'path' => $path,
			'parts' => $parts,
			'namespace' => "\\" . trim($namepsace, '\\')
		);

		return $this;
	}


	/**
	 * @return App
	 */
	public function redirect($from, $to, $method = null) {
		$this->redirects[$from] = array(
			'to' => $to,
			'method' => $method
		);
		return $this;
	}

	public function run() {
		$uri = filter_input(INPUT_SERVER, 'REDIRECT_URL');
		$folder = dirname(filter_input(INPUT_SERVER, 'SCRIPT_NAME'));
		$path = $this->diffPaths($uri, $folder);
		$this->runPath($path);
	}

	public function runPath($path) {
		$path = $this->getRedirectPath($path);
		$parts = $this->getPathParts($path);
		$p = $this->getMatchParts($parts);
		if (!$p) {
			HServer::sendNotFound();
		}
		$remaining = $this->diffParts($parts, $p['parts']);
		$controllerName = array_shift($remaining);

		if (!$controllerName) {
			HServer::sendNotFound();
		}

		$className = $p['namespace'] . "\\" . ucfirst($controllerName) . 'Controller';
		$exists = false;
		try {
			$exists = class_exists($className);
		}
		catch (JNMException $ex) {
		}

		if (!$exists) {
			HServer::sendNotFound();
		}

		$route = new Route('/' . implode('/', $remaining));
		$controller = new $className($route);
		$route->run($controller);

		HServer::sendNotFound();
	}

	private function getRedirectPath($path) {
		$partsCurrent = $this->getPathParts($path);
		foreach ($this->redirects as $from => $redirect) {
			$method = $redirect['method'];
			$to = $redirect['to'];
			if ($method && $this->request->getMethod() != $method) {
				continue;
			}
			$partsFrom = $this->getPathParts($from);
			if ($this->partsContains($partsCurrent, $partsFrom)) {
				//a/5/e ($partsCurrent)
				//a/:id ($partsFrom) => /c/$1 ($partsTo)
				//c/5/e (result)
				$partsTo = $this->getPathParts($to);
				$partsTo = $this->replacePartsVars($partsTo, $partsFrom, $partsCurrent);
				$partsDiff = $this->diffParts($partsCurrent, $partsFrom);
				$partsTo = array_merge($partsTo, $partsDiff);
				return '/' . implode('/', $partsTo);
			}
		}
		return $path;
	}

	private function diffPaths($path1, $path2) {
		$parts = $this->diffParts(
			$this->getPathParts($path1),
			$this->getPathParts($path2)
		);
		return '/' . implode('/', $parts);
	}

	private function replacePartsVars($partsTo, $partsFrom, $partsCurrent) {
		$map = array();
		for ($i=0; $i<count($partsFrom); $i++) {
			if (strpos($partsFrom[$i], ':') === 0) {
				$map[] = $partsCurrent[$i];
			}
		}
		if ($map) {
			for ($i=0; $i<count($partsTo); $i++) {
				$partsTo[$i] = preg_replace_callback('#\$([0-9]+)#', function($match) use ($map) {
					$i = $match[1]-1;
					return isset($map[$i]) ? $map[$i] : $match[0];
				}, $partsTo[$i]);
			}
		}
		return $partsTo;
	}

	private function diffParts($parts1, $parts2) {
		return $this->consumeParts($parts1, count($parts2));
	}

	private function consumeParts($parts, $count) {
		return array_slice($parts, $count);
	}

	private function getMatchParts($parts) {
		foreach ($this->paths as $p) {
			if ($this->partsContains($parts, $p['parts'])) {
				return $p;
			}
		}
		return null;
	}

	private function partsContains($parts1, $parts2) {
		for ($i = 0; $i < count($parts2); $i++) {
			if (!isset($parts2[$i])) {
				return false;
			}
			if ($parts2[$i] != $parts1[$i] && strpos($parts2[$i], ':') === false) {
				return false;
			}
		}
		return true;
	}

	private function getPathParts($path) {
		$path = $this->fixPath($path);
		if (!$path) {
			return array();
		}
		return explode('/', $path);
	}

	private function fixPath($path) {
		return preg_replace('#/+#', '/', trim($path, '/'));
	}
}
