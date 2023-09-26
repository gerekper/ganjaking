<?php

namespace ACP\Helper;

use LogicException;

final class FilterButtonFactory {

	const SCREEN_USERS = 'users';
	const SCREEN_TAXONOMY = 'taxonomy';

	/**
	 * @var FilterButton[]
	 */
	private static $instances = [];

	/**
	 * @param string $screen
	 *
	 * @return FilterButton
	 */
	public static function create( $screen ) {
		if ( ! isset( self::$instances[ $screen ] ) ) {
			switch ( $screen ) {
				case self::SCREEN_USERS:
					self::$instances[ $screen ] = new FilterButton\Users( $screen );

					break;
				case self::SCREEN_TAXONOMY:
					self::$instances[ $screen ] = new FilterButton\Taxonomy( $screen );

					break;
				default:
					throw new LogicException( 'Invalid screen found.' );
			}
		}

		return self::$instances[ $screen ];
	}

}