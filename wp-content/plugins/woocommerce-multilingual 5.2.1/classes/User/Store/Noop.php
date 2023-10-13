<?php

namespace WCML\User\Store;


class Noop {

	/**
	 * @param string $key
	 *
	 * @return null
	 */
	public function get( $key ) {
		return null;
	}

	public function set( $key, $value ) {
	}
}