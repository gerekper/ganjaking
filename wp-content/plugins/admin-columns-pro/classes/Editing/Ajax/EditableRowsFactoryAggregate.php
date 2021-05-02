<?php

namespace ACP\Editing\Ajax;

use AC;

final class EditableRowsFactoryAggregate implements EditableRowsFactoryInterface {

	/**
	 * @var EditableRowsFactoryInterface[]
	 */
	private static $factories;

	public static function add_factory( EditableRowsFactoryInterface $factory ) {
		self::$factories[] = $factory;
	}

	public static function create( AC\Request $request, AC\ListScreen $list_screen ) {
		foreach ( self::$factories as $factory ) {
			$rows = $factory::create( $request, $list_screen );

			if ( $rows ) {
				break;
			}
		}

		return $rows;
	}

}