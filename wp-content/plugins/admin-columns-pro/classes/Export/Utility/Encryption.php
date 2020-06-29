<?php

namespace ACP\Export\Utility;

/**
 * Utility functions for encryption
 * @since 1.0
 */
class Encryption {

	/**
	 * Generate a random encryption key
	 * @return string Generated encryption key
	 * @since 1.0
	 */
	public static function generate_key() {
		return md5( microtime( true ) . wp_rand() );
	}

}
