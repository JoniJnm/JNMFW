<?php

namespace JNMFW;

use JNMFW\classes\Request;
use JNMFW\classes\Server;

class BaseController {
	/**
	 * @var Request
	 */
	protected $request;
	
	/**
	 * @var Server
	 */
	protected $server;
	
	public function __construct() {
		classes\databases\DBFactory::getInstance()->transactionBegin();
		$this->request = Request::getInstance();
		$this->request->setStrictMode(true);
		$this->server = Server::getInstance();
	}
}
