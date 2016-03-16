<?php

namespace JNMFW\helpers;

use JNMFW\classes\Server;

abstract class HServer
{
	static public function sendInvalidParam($key, $log = true)
	{
		$server = Server::getInstance();
		$server->sendInvalidParameter($key, $log);
	}

	static public function sendNotFound($msg_log = null)
	{
		Server::getInstance()->sendNotFound($msg_log);
	}
}