<?php

namespace JNMFW\classes;

use JNMFW\exceptions\JNMException;
use JNMFW\helpers\HServer;

class App
{
	private $paths = array();
	private $redirects = array();

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
	public function redirect($from, $to) {
		$this->redirects[$from] = $to;
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
		$parts1 = $this->getPathParts($path);
		foreach ($this->redirects as $from => $to) {
			$parts2 = $this->getPathParts($from);
			if ($this->isPartsMatching($parts1, $parts2)) {
				$parts1 = $this->diffParts($parts1, $parts2);
				$partsTo = $this->getPathParts($to);
				foreach ($parts1 as $part) {
					$partsTo[] = $part;
				}
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

	private function diffParts($parts1, $parts2) {
		return $this->consumeParts($parts1, count($parts2));
	}

	private function consumeParts($parts, $count) {
		return array_slice($parts, $count);
	}

	private function getMatchParts($parts) {
		foreach ($this->paths as $p) {
			if ($this->isPartsMatching($parts, $p['parts'])) {
				return $p;
			}
		}
		return null;
	}

	private function isPartsMatching($parts1, $parts2) {
		for ($i = 0; $i < count($parts2); $i++) {
			if (!isset($parts2[$i]) || $parts2[$i] != $parts1[$i]) {
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
