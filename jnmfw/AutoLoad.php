<?php

namespace JNMFW;

function jnmfw_autoload($className, $base, $root) {
	if (\strpos($className, $base.'\\') !== 0) return;
	$path = \str_replace('\\', '/', \substr($className, strlen($base))).'.php';
	$file = $root.$path;
	if (!\file_exists($file)) {
		throw new \RuntimeException("No se puede cargar el archivo $file para la clase $className");
	}
	require($file);
}

\spl_autoload_register(function ($className) {
	jnmfw_autoload($className, 'JNMFW', __DIR__);
});