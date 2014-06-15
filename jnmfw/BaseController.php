<?php

namespace JNMFW;

use JNMFW\classes\Request;
class BaseController {
	/**
	 * @var Request
	 */
	protected $request;
	
	public function __construct() {
		classes\databases\DBFactory::getInstance()->transaccionBegin();
		$this->request = Request::getInstance();
		$this->request->setStrictMode(true);
	}
}
