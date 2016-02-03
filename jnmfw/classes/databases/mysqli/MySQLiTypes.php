<?php

namespace JNMFW\classes\databases\mysqli;

abstract class MySQLiTypes {
	const INTEGER = 'integer';
	const FLOAT = 'float';
	
	public static $TYPES = array(
		MYSQLI_TYPE_DECIMAL => self::FLOAT,
		MYSQLI_TYPE_NEWDECIMAL => self::FLOAT,
		MYSQLI_TYPE_TINY => self::INTEGER,
		MYSQLI_TYPE_SHORT => self::INTEGER,
		MYSQLI_TYPE_LONG => self::INTEGER,
		MYSQLI_TYPE_FLOAT => self::FLOAT,
		MYSQLI_TYPE_DOUBLE => self::FLOAT,
		MYSQLI_TYPE_LONGLONG => self::INTEGER,
		MYSQLI_TYPE_INT24 => self::INTEGER
	);
}
