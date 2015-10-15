<?php

namespace JNMFW\classes\databases;

use JNMFW\helpers\HServer;
use JNMFW\classes\databases\DBDriver;
use JNMFW\classes\databases\DBAdapter;

// *************************************************************************
// Clase Base de Datos - gestiona la información de la Base de Datos
// *************************************************************************

abstract class DBFactory {
	/**
	 * @var DBConnection[]
	 */
	private static $instances = array();
	
	/**
	 * Registra una instancia de base de datos para poder luego obtenerla con getInstance($name)
	 * @param string $name nombre de la instancia
	 * @return bool true si se pudo crear la instancia, false en caso contrario
	 */
	static public function registerIntance($name, DBDriver $driver, DBAdapter $adapter) {
		if (!\preg_match('#^[\w]+$#', $name)) {
			HServer::sendServerError("El nombre de la instancia (".$name.") es inválido");
		}
		if ($adapter) {
			self::$instances[$name] = $driver->getConnection($adapter);
			return true;
		}
		HServer::sendServerError("Imposible conectar a la DB"); //no continuar, no se puedo conectar a la db
		return false;
	}
	
	/**
	 * Does a transaction rollback in all opened connections
	 * This method is used when an error occurs
	 */
	static public function rollbackAllConnections() {
		foreach (self::$instances as $instance) {
			$instance->transactionRollback();
		}
	}
	
	/**
	 * Does a transaction commit in all opened connections
	 * This method is used when the connection ends successfully
	 */
	static public function commitAllConnections() {
		foreach (self::$instances as $instance) {
			$instance->transactionCommit();
		}
	}
	
	/**
	 * Recupera una instacia creada anteriormente con registerInstance()
	 * @param string $name nombre de la instancia
	 * @return DBConnection La instancia del objeto de la base de datos
	 */
	static public function getInstance($name = 'default') {
		if (self::instanceExists($name)) return self::$instances[$name];
		HServer::sendServerError("No se ha registrado una instancia para '".$name."'");
	}
	
	/**
	 * Registra la instancia por defecto que se obtendrá al llamar a getInstance() sin parámetros
	 * @return bool true si se pudo crear la instancia, false en caso contrario
	 */
	static public function registerDefaultInstance(DBDriver $driver, DBAdapter $adapter) {
		return self::registerIntance('default', $driver, $adapter);
	}
	
	/**
	 * @param string $name nombre de la instancia
	 * @return boolean true si existe, false en caso contrario
	 */
	static public function instanceExists($name) {
		return isset(self::$instances[$name]);
	}
}