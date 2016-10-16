<?php

namespace JNMFW\classes;

class Response
{
	private $data = null;
	private $status = 200;

	/**
	 * @param \stdClass|array|null $data
	 */
	public function __construct($data = null) {
		if (is_object($data)) {
			$this->data = $data;
		}
		elseif (is_array($data)) {
			$this->data = (object)$data;
		}
		elseif ($data !== null) {
			throw new \InvalidArgumentException('$data must be an object');
		}
		else {
			$this->data = new \stdClass();
		}
	}

	/**
	 * @param int $status
	 * @return $this
	 */
	public function setStatus($status) {
		$this->status = $status;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @return \stdClass
	 */
	public function getOutputData() {
		return $this->data;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return $this
	 */
	public function put($key, $value) {
		$this->data->$key = $value;
		return $this;
	}

	/**
	 * @param string $msg
	 * @return $this
	 */
	public function setDialogWarn($msg) {
		return $this->setDialog('warn', $msg);
	}

	/**
	 * @param string $msg
	 * @return $this
	 */
	public function setDialogAlert($msg) {
		return $this->setDialog('alert', $msg);
	}

	/**
	 * @param string $msg
	 * @return $this
	 */
	public function setDialogError($msg) {
		return $this->setDialog('error', $msg);
	}

	/**
	 * @param string $type
	 * @param string $msg
	 * @return $this
	 */
	protected function setDialog($type, $msg) {
		$dialog = array(
			'type' => $type,
			'message' => $msg
		);
		$this->put('dialog', $dialog);
		return $this;
	}
}