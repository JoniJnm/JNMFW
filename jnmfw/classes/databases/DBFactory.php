<?php

namespace JNMFW\classes\databases;

use JNMFW\helpers\HServer;

// *************************************************************************
// Clase Base de Datos - gestiona la información de la Base de Datos
// *************************************************************************

abstract class DBFactory {
	const DB_DRIVER_MySQLi = 1;
	const DB_DRIVER_DEFAULT = self::DB_DRIVER_MySQLi;
	
	private static $drivers = array();
	
	private static $instances = array();
	
	/**
	 * Registra una instancia de base de datos para poder luego obtenerla con getInstance($name)
	 * @param string $name nombre de la instancia
	 * @param string $host El servidor (generalmente localhost)
	 * @param string $user El usuario para conexión
	 * @param string $pass La contraseña del usuario
	 * @param string $dbname El nombre de la base de datos donde conectarse (vacio para no conectarse a una db en concreto)
	 * @return bool true si se pudo crear la instancia, false en caso contrario
	 */
	static public function registerIntance($name, $host, $user, $pass, $dbname='', $driver = self::DB_DRIVER_DEFAULT) {
		if (!\preg_match('#^[\w]+$#', $name)) {
			HServer::sendServerError("El nombre de la instancia (".$name.") es inválido");
		}
		if (!in_array($driver, array(self::DB_DRIVER_MySQLi))) {
			HServer::sendServerError("No existe el tipo de base de datos '".$driver."'");
		}
		switch($driver) {
			case self::DB_DRIVER_MySQLi:
				$className = '\JNMFW\classes\databases\mysqli\MySQLiDriver';
				break;
			default: 
				HServer::sendServerError("Unkown driver $driver");
				break;
		}
		if (!isset(self::$drivers[$className])) {
			self::$drivers[$className] = new $className;
		}
		$driverInstance = self::$drivers[$className];
		$adapter = $driverInstance->getAdapter($host, $user, $pass, $dbname);
		if ($adapter) {
			self::$instances[$name] = $driverInstance->getConnection($adapter);
			return true;
		}
		HServer::sendServerError("Imposible conectar a la DB"); //no continuar, no se puedo conectar a la db
		return false;
	}
	
	/**
	 * Recupera una instacia creada anteriormente con registerInstance()
	 * @param string $name nombre de la instancia
	 * @return DBConnection La instancia del objeto de la base de datos
	 */
	static public function getInstance($name = 'default') {
		if (self::instanceExists($name)) return self::$instances[$name];
		if ($name == 'default') {
			self::registerDefaultInstance();
			return self::getInstance($name);
		}
		else {
			HServer::sendServerError("No se ha registrado una instancia para '".$name."'");
		}
	}
	
	/**
	 * Registra la instancia por defecto que se obtendrá al llamar a getInstance() sin parámetros
	 * @return bool true si se pudo crear la instancia, false en caso contrario
	 */
	static public function registerDefaultInstance($server, $user, $pass, $db, $driver = self::DB_DRIVER_DEFAULT) {
		return self::registerIntance('default', $server, $user, $pass, $db, $driver);
	}
	
	/**
	 * @param string $name nombre de la instancia
	 * @return boolean true si existe, false en caso contrario
	 */
	static public function instanceExists($name) {
		return isset(self::$instances[$name]);
	}
}