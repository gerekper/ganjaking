<?php

namespace ACP\Editing\Ajax;

use AC;
use ACP\ListScreen;

final class EditableRowsFactory implements EditableRowsFactoryInterface {

	/**
	 * @param AC\Request    $request
	 * @param AC\ListScreen $list_screen
	 *
	 * @return EditableRows|null
	 */
	public static function create( AC\Request $request, AC\ListScreen $list_screen ) {
		switch ( true ) {
			case $list_screen instanceof ListScreen\Post:
				return new EditableRows\Post( $request, $list_screen->editing() );
			case $list_screen instanceof ListScreen\Media:
				return new EditableRows\Post( $request, $list_screen->editing() );
			case $list_screen instanceof ListScreen\User:
				return new EditableRows\User( $request, $list_screen->editing() );
			case $list_screen instanceof ListScreen\Taxonomy:
				return new EditableRows\Taxonomy( $request, $list_screen->editing() );
			case $list_screen instanceof ListScreen\Comment:
				return new EditableRows\Comment( $request, $list_screen->editing() );
			default :
				return null;
		}
	}

}