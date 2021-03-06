<?php

namespace JNMFW\classes;

use JNMFW\helpers\HServer;

class Filter
{
	private $data = array();
	private $strict = false;

	public function __construct($data) {
		$this->data = $data;
	}

	public function setStrictMode($strict) {
		$this->strict = $strict;
	}

	protected function isStrict() {
		return $this->strict;
	}

	public function putData($key, $value) {
		$this->data[$key] = $value;
	}

	protected function isset_else($key, $def) {
		if (strpos($key, '[') !== false) {
			$key = str_replace(']', '', $key);
			$data = $this->data;
			$keys = explode('[', $key);
			foreach ($keys as $key) {
				if (isset($data[$key])) {
					$data = $data[$key];
				}
				else {
					return $def;
				}
			}
			return $data ? $data : $def;
		}
		else {
			return isset($this->data[$key]) ? $this->data[$key] : $def;
		}
	}

	/**
	 * Checks if key value is set
	 * @param string $key
	 * @return boolean true si está seteado false en caso contrario
	 */
	public function is_set($key) {
		return isset($this->data[$key]);
	}

	/**
	 * Devuelve true cuando la key no está seteada o cuando su valor es vacío.
	 * Se considera vacío cuando tiene uno de los siguientes valores:
	 * • "" (una cadena vacía)
	 * • 0 (0 como un integer)
	 * • 0.0 (0 como un float)
	 * • "0" (0 como un string)
	 * • NULL
	 * • FALSE
	 * • array() (un array vacío)
	 * • $var; (una variable declarada, pero sin un valor)
	 *
	 * @param string $key
	 * @param bool $cero_is_empty Si es false, los siguientes valores no se considerarán vacío: 0, 0.0, '0'
	 * @return boolean
	 */
	public function is_empty($key, $cero_is_empty = true) {
		return empty($this->data[$key]) ||
		(
			!$cero_is_empty && isset($this->data[$key]) &&
			in_array($this->data[$key], array(0, 0.0, '0'), true)
		);
	}

	/**
	 * Get value without HTML tags
	 * @param string $key
	 * @param string $def
	 * @return string
	 */
	public function getString($key, $def = '', $maxLegnth = 0) {
		$source = $this->isset_else($key, $def);
		$out = \trim(\strip_tags($source));
		if ($out && (!$maxLegnth || strlen($maxLegnth) <= $maxLegnth)) {
			return $out;
		}
		elseif ($this->isStrict()) {
			HServer::sendInvalidParam($key);
		}
		else {
			return $def;
		}
	}

	/**
	 * Get value with HTML safe filter
	 * @param string $key
	 * @param string $def
	 * @return string
	 */
	public function getHTML($key, $def = '') {
		$source = $this->isset_else($key, $def);
		//TODO: check HTML
		if ($source) {
			return $source;
		}
		elseif ($this->isStrict()) {
			HServer::sendInvalidParam($key);
		}
		else {
			return $def;
		}
	}

	private function validateInt($val, $min_range, $max_range) {
		$options = array();
		if ($min_range !== null) {
			$options['min_range'] = $min_range;
		}
		if ($max_range !== null) {
			$options['max_range'] = $max_range;
		}
		return \filter_var($val, \FILTER_VALIDATE_INT, array('options' => $options));
	}

	/**
	 * @param string $key
	 * @param integer $def
	 * @param null $min_range
	 * @param null $max_range
	 * @return int
	 */
	public function getInt($key, $def = 0, $min_range = null, $max_range = null) {
		$source = $this->isset_else($key, null);
		$result = $this->validateInt($source, $min_range, $max_range);
		if ($result !== false) {
			return $result;
		}
		elseif ($this->isStrict()) {
			HServer::sendInvalidParam($key);
		}
		else {
			return $def;
		}
	}

	/**
	 * @param string $key
	 * @param null $min_range
	 * @param null $max_range
	 * @return int[]
	 */
	public function getInts($key, $min_range = null, $max_range = null) {
		$source = $this->getArray($key);
		$out = array();
		foreach ($source as $value) {
			$result = $this->validateInt($value, $min_range, $max_range);
			if ($result !== false) {
				$out[] = $result;
			}
			elseif ($this->isStrict()) {
				HServer::sendInvalidParam($key);
			}
			else {
				return array();
			}
		}
		return $out;
	}

	/**
	 * @param string $key
	 * @param integer $def
	 * @param null $max_range
	 * @return int
	 */
	public function getUInt($key, $def = 0, $max_range = null) {
		return $this->getInt($key, $def, 0, $max_range);
	}

	/**
	 * @param string $key
	 * @param null $max_range
	 * @return int
	 */
	public function getUInts($key, $max_range = null) {
		return $this->getInts($key, 0, $max_range);
	}

	/**
	 * @param string $key
	 * @param float|int $def
	 * @param null $min_range
	 * @param null $max_range
	 * @return float
	 */
	public function getFloat($key, $def = 0, $min_range = null, $max_range = null) {
		$source = $this->isset_else($key, null);
		$source = str_replace(',', '.', $source);
		$result = \filter_var($source, \FILTER_VALIDATE_FLOAT);
		if ($result === false) {
			return $def;
		}
		if ($min_range !== null && $result < $min_range) {
			return $def;
		}
		if ($max_range !== null && $result > $max_range) {
			return $def;
		}

		if ($result !== false) {
			return $result;
		}
		elseif ($this->isStrict()) {
			HServer::sendInvalidParam($key);
		}
		else {
			return $def;
		}
	}

	/**
	 * @param string $key
	 * @param float|int $def
	 * @param null $max_range
	 * @return float
	 */
	public function getUFloat($key, $def = 0, $max_range = null) {
		return $this->getFloat($key, $def, 0, $max_range);
	}

	/**
	 * Devuelve TRUE para "1", "true", "on" y "yes". Devuelve FALSE en caso contrario.
	 * @param string $key
	 * @param boolean $def
	 * @return boolean
	 */
	public function getBool($key, $def = false) {
		$source = $this->isset_else($key, null);
		$result = \filter_var($source, \FILTER_VALIDATE_BOOLEAN, array('flags' => \FILTER_NULL_ON_FAILURE));
		if ($result !== null) {
			return $result;
		}
		elseif ($this->isStrict()) {
			HServer::sendInvalidParam($key);
		}
		else {
			return $def;
		}
	}

	public function getRegex($regex, $key, $def = '', $caseSensitive = false) {
		$source = $this->isset_else($key, $def);
		$modif = $caseSensitive ? '' : 'i';
		if (\preg_match("/^{$regex}$/{$modif}", $source)) {
			return $source;
		}
		elseif ($this->isStrict()) {
			HServer::sendInvalidParam($key);
		}
		else {
			return $def;
		}
	}

	/**
	 * Only allow characters a-z
	 * @param string $key
	 * @param string $def
	 * @return string
	 */
	public function getWord($key, $def = '') {
		return $this->getRegex('[A-Z]+', $key, $def);
	}

	/**
	 * Allow a-z, 0-9, underscore, dot, dash
	 * @param string $key
	 * @param string $def
	 * @return string
	 */
	public function getCmd($key, $def = '') {
		return $this->getRegex('[A-Z0-9_\.\-]+', $key, $def);
	}

	/**
	 * Get sha1 token
	 * @param string $key
	 * @return string
	 */
	public function getSha1($key, $def = '') {
		return $this->getRegex('[a-f0-9]{40}', $key, $def, false);
	}

	/**
	 * Devuelve una dirección IP
	 * @param string $key
	 * @param string $def
	 * @param bool $only_IPV4
	 * @return string
	 */
	public function getIP($key, $def = '', $only_IPV4 = false) {
		$source = $this->isset_else($key, $def);
		$options = array();
		if ($only_IPV4) {
			$options['flags'] = \FILTER_FLAG_IPV4;
		}
		$result = \filter_var($source, \FILTER_VALIDATE_IP, $options);
		if ($result) {
			return $result;
		}
		elseif ($this->isStrict()) {
			HServer::sendInvalidParam($key);
		}
		else {
			return $def;
		}
	}

	/**
	 * Devuelve una dirección E-Mail.
	 * @param string $key
	 * @param string $def
	 * @return string
	 */
	public function getEmail($key, $def = '') {
		$source = $this->isset_else($key, $def);
		$result = \filter_var($source, \FILTER_VALIDATE_EMAIL);
		if ($result) {
			return $result;
		}
		elseif ($this->isStrict()) {
			HServer::sendInvalidParam($key);
		}
		else {
			return $def;
		}
	}

	/**
	 * Devuelve una URL
	 * @param string $key
	 * @param string $def
	 * @return String
	 */
	public function getUrl($key, $def = '') {
		$source = $this->isset_else($key, $def);
		$result = \filter_var($source, \FILTER_VALIDATE_URL);
		if ($result) {
			return $result;
		}
		elseif ($this->isStrict()) {
			HServer::sendInvalidParam($key);
		}
		else {
			return $def;
		}
	}

	/**
	 * Devuelve un array, null si es inválido o no existe
	 * @param string $key
	 * @return mixed
	 */
	public function getJSON($key) {
		$source = $this->isset_else($key, null);
		$result = @json_decode($source, true);
		if ($result) {
			return $result;
		}
		elseif ($this->isStrict()) {
			HServer::sendInvalidParam($key);
		}
		else {
			return null;
		}
	}

	/**
	 * Devuelve un valor sin filtro
	 * @param string $key
	 * @param string $def
	 * @return String
	 */
	public function get($key, $def = '') {
		$source = $this->isset_else($key, null);
		if ($source) {
			return $source;
		}
		elseif ($this->isStrict()) {
			HServer::sendInvalidParam($key);
		}
		else {
			return $def;
		}
	}

	/**
	 * Devuelve un array sin filtro
	 * @param string $key
	 * @return array
	 */
	public function getArray($key) {
		$source = $this->isset_else($key, null);
		if (is_array($source)) {
			return $source;
		}
		elseif ($this->isStrict()) {
			HServer::sendInvalidParam($key);
		}
		else {
			return array();
		}
	}
}