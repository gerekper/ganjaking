<?php

namespace ACP\Access;

class Platform {

	public static function is_local() {
		return isset( $_SERVER['REMOTE_ADDR'] ) && in_array( $_SERVER['REMOTE_ADDR'], [ '127.0.0.1', '::1' ], true );
	}

}