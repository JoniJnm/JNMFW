<?php

namespace JNMFW\helpers;

abstract class HDate
{
	/**
	 * Devuelve la fecha y hora en formato YYYY-mm-dd
	 * @param string $date En cualquier formato válido: http://www.php.net/manual/es/datetime.formats.php
	 * return string fecha formateada
	 * @return string
	 */
	static public function formatDate($date) {
		return self::format_date($date, 'Y-m-d');
	}

	/**
	 * Devuelve la fecha y hora en formato yyyy-mm-dd hh:mm:ss
	 * @param string $date En cualquier formato válido: http://www.php.net/manual/es/datetime.formats.php
	 * return string fecha formateada
	 * @return string
	 */
	static public function formatDateTime($date) {
		return self::format_date($date, 'Y-m-d H:i:s');
	}

	static public function getTimestamp($date) {
		$fech = self::getDate($date);
		return $fech->getTimestamp();
	}

	static private function format_date($date, $formato) {
		$fech = self::getDate($date);
		//$fech->setTimezone(static::getDateTimeZoneUTC());
		return $fech->format($formato);
	}

	/**
	 * @param int|string $dateStr date as string or timestamp
	 * @return \DateTime
	 */
	static public function getDate($dateStr, $timezone = null) {
		if (\is_numeric($dateStr)) {
			$fech = new \DateTime(null, $timezone);
			$fech->setTimestamp($dateStr);
		}
		else {
			$fech = new \DateTime($dateStr, $timezone);
		}
		return $fech;
	}

	static private function getDateTimeZoneUTC() {
		static $timezone = null;
		if ($timezone === null) {
			$timezone = new \DateTimeZone("UTC");
		}
		return $timezone;
	}
}
