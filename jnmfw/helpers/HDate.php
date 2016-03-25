<?php

namespace JNMFW\helpers;

abstract class HDate
{
	static public function getNullDate()
	{
		return '0000-00-00';
	}

	static public function getNullDateTime()
	{
		return '0000-00-00 00:00:00';
	}

	static public function isNullDate($date)
	{
		return empty($date) || $date == self::getNullDate() || $date == self::getNullDateTime();
	}

	/**
	 * Devuelve la fecha y hora en formato YYYY-mm-dd
	 * @param string $date En cualquier formato válido: http://www.php.net/manual/es/datetime.formats.php
	 * return string fecha formateada
	 */
	static public function formatDate($date)
	{
		return self::format_date($date, 'Y-m-d');
	}

	/**
	 * Devuelve la fecha y hora en formato yyyy-mm-dd hh:mm:ss
	 * @param string $date En cualquier formato válido: http://www.php.net/manual/es/datetime.formats.php
	 * return string fecha formateada
	 */
	static public function formatDateTime($date)
	{
		return self::format_date($date, 'Y-m-d H:i:s');
	}

	static private function format_date($date, $formato)
	{
		if (\is_numeric($date)) {
			$fech = new \DateTime(null);
			$fech->setTimestamp($date);
		}
		else {
			$fech = new \DateTime($date);
		}
		$fech->setTimezone(static::getDateTimeZoneUTC());
		return $fech->format($formato);
	}

	static private function getDateTimeZoneUTC()
	{
		static $timezone = null;
		if ($timezone === null) {
			$timezone = new \DateTimeZone("UTC");
		}
		return $timezone;
	}
}
