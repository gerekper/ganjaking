<?php

namespace WCML\User\Store;


interface Strategy {

	/**
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get( $key );

	/**
	 * @param string   $key
	 * @param mixed    $value
	 */
	public function set( $key, $value );
}
