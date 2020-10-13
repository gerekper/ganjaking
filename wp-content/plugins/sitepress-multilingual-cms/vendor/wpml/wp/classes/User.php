<?php

namespace WPML\LIB\WP;

use function WPML\FP\curryN;
use function WPML\FP\partialRight;

class User {

	/**
	 * @return int
	 */
	public static function getCurrentId() {
		return get_current_user_id();
	}

	/**
	 * Curried function to update the user meta.
	 *
	 * @param int    $userId
	 * @param string $metaKey
	 * @param mixed  $metaValue
	 *
	 * @return callable|int|bool
	 */
	public static function updateMeta( $userId = null, $metaKey = null, $metaValue = null ) {
		return call_user_func_array( curryN( 3, 'update_user_meta' ), func_get_args() );
	}

	/**
	 * Curried function to get the user meta
	 *
	 * @param int    $userId
	 * @param string $metaKey
	 *
	 * @return callable|mixed
	 */
	public static function getMetaSingle( $userId = null, $metaKey = null ) {
		return call_user_func_array( curryN( 2, partialRight( 'get_user_meta', true ) ), func_get_args() );
	}


}
