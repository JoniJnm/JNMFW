<?php

namespace JNMFW\helpers;

use JNMFW\helpers\HLang;
use JNMFW\classes\Server;

abstract class HServer {
	static public function sendInvalidParam($key, $log = true) {
		$server = Server::getInstance();
		$msg_user = HLang::get('INVALID_'.strtoupper($key));
		$server->sendInvalidRequest($msg_user, $key, $log);
	}
	
	static public function sendNotFound($msg_log) {
		Server::getInstance()->sendNotFound($msg_log);
	}
}