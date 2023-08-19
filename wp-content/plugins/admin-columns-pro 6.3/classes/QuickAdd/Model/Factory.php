<?php

namespace ACP\QuickAdd\Model;

use AC\ListScreen;

class Factory {

	/**
	 * @var ModelFactory[]
	 */
	private static $factories = [];

	public static function add_factory( ModelFactory $factory ) {
		self::$factories[] = $factory;
	}

	/**
	 * @param ListScreen $list_screen
	 *
	 * @return Create|null
	 */
	public static function create( ListScreen $list_screen ) {
		foreach ( array_reverse( self::$factories ) as $factory ) {
			$model = $factory->create( $list_screen );

			if ( $model ) {
				return $model;
			}
		}

		return null;
	}

}