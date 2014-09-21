<?php

namespace JNMFW\helpers;

abstract class HUtils {
	static public function createToken() {
		return \sha1(\mt_rand());
	}
}
