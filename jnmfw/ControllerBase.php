<?php

namespace JNMFW;

use JNMFW\classes\Request;
use JNMFW\classes\Route;
use JNMFW\classes\Server;

class ControllerBase
{
	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var Server
	 */
	protected $server;

	/**
	 * @var Route
	 */
	protected $route;

	public function __construct(Route $route) {
		classes\databases\DBFactory::getInstance()->transactionBegin();
		$this->request = Request::getInstance();
		$this->request->setStrictMode(true);
		$this->server = Server::getInstance();
		$this->route = $route;
	}
}
