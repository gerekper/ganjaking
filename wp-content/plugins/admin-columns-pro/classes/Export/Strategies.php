<?php

namespace ACP\Export;

/**
 * Contains all available ACP_Export_Strategy instances
 */
class Strategies {

	/**
	 * Registered list screens supporting export functionality
	 * @since 1.0
	 * @var Strategy[]
	 */
	protected static $strategies;

	/**
	 * @param Strategy $strategy
	 *
	 * @since 1.0
	 */
	public static function register_strategy( Strategy $strategy ) {
		self::$strategies[ $strategy->get_list_screen()->get_key() ] = $strategy;
	}

	/**
	 * @param $key
	 *
	 * @return Strategy|null
	 * @since 1.0
	 */
	public static function get_strategy( $key ) {
		if ( isset( self::$strategies[ $key ] ) ) {
			return self::$strategies[ $key ];
		}

		return null;
	}

}