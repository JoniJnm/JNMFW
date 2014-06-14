<?php

namespace JNMFW;

use JNMFW\classes\Request;
class BaseController {
	/**
	 * @var Request
	 */
	protected $request;
	
	public function __construct() {
		$this->request = Request::getInstance();
		$this->request->setStrictMode(true);
	}
}
