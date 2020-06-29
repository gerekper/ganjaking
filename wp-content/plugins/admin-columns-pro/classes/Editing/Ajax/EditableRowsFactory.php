<?php

namespace ACP\Editing\Ajax;

use AC;
use ACP\ListScreen\Comment;
use ACP\ListScreen\Media;
use ACP\ListScreen\Post;
use ACP\ListScreen\Taxonomy;
use ACP\ListScreen\User;

final class EditableRowsFactory {

	/**
	 * @param AC\Request    $request
	 * @param AC\ListScreen $list_screen
	 *
	 * @return EditableRows|false
	 */
	public static function create( AC\Request $request, AC\ListScreen $list_screen ) {
		switch ( true ) {
			case $list_screen instanceof Post:
				return new EditableRows\Post( $request, $list_screen->editing() );
			case $list_screen instanceof Media:
				return new EditableRows\Post( $request, $list_screen->editing() );
			case $list_screen instanceof User:
				return new EditableRows\User( $request, $list_screen->editing() );
			case $list_screen instanceof Taxonomy:
				return new EditableRows\Taxonomy( $request, $list_screen->editing() );
			case $list_screen instanceof Comment:
				return new EditableRows\Comment( $request, $list_screen->editing() );
		}

		return false;
	}

}