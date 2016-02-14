<?php

namespace JNMFW\classes\databases;


// *************************************************************************
// Clase Base de Datos - gestiona la información de la Base de Datos
// *************************************************************************

abstract class DBFactory {
	/**
	 * @var DBConnection[]
	 */
	private static $connections = array();
	
	/**
	 * @var DBDriver[]
	 */
	private static $drivers = array();
	
	/**
	 * Registra una instancia de base de datos para poder luego obtenerla con getInstance($name)
	 * @param string $name nombre de la instancia
	 */
	static public function registerIntance($name, DBDriver $driver) {
		if (!\preg_match('#^[\w]+$#', $name)) {
			throw new \InvalidArgumentException("El nombre de la instancia (".$name.") es inválido");
		}
		elseif (self::instanceExists($name)) {
			throw new \LogicException("La instancia (".$name.") ya existe");
		}
		self::$drivers[$name] = $driver;
	}
	
	/**
	 * Does a transaction rollback in all opened connections
	 * This method is used when an error occurs
	 */
	static public function rollbackAllConnections() {
		foreach (self::$connections as $connection) {
			$connection->transactionRollback();
		}
	}
	
	/**
	 * Does a transaction commit in all opened connections
	 * This method is used when the connection ends successfully
	 */
	static public function commitAllConnections() {
		foreach (self::$connections as $connection) {
			$connection->transactionCommit();
		}
	}
	
	/**
	 * Recupera una instacia creada anteriormente con registerInstance()
	 * @param string $name nombre de la instancia
	 * @param boolean $newConnection create new DB connection
	 * @return DBConnection La instancia del objeto de la base de datos
	 */
	static public function getInstance($name = 'default', $newConnection = false) {
		if (!self::instanceExists($name)) {
			throw new \InvalidArgumentException("La instancia '".$name."' no existe");
		}
		if (!isset(self::$connections[$name]) || $newConnection) {
			$driver = self::$drivers[$name];
			$connection = $driver->createConnection();
			if ($newConnection) {
				return $connection;
			}
			elseif (!isset(self::$connections[$name])) {
				self::$connections[$name] = $connection;
			}
		}
		return self::$connections[$name];
	}
	
	/**
	 * Registra la instancia por defecto que se obtendrá al llamar a getInstance() sin parámetros
	 */
	static public function registerDefaultInstance(DBDriver $driver) {
		self::registerIntance('default', $driver);
	}
	
	/**
	 * @param string $name nombre de la instancia
	 * @return boolean true si existe, false en caso contrario
	 */
	static public function instanceExists($name) {
		return isset(self::$drivers[$name]);
	}
}