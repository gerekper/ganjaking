<?php

namespace ACP\Editing\Ajax;

use AC;
use AC\Request;
use ACP\ListScreen\Comment;
use ACP\ListScreen\Media;
use ACP\ListScreen\Post;
use ACP\ListScreen\Taxonomy;
use ACP\ListScreen\User;

final class TableRowsFactory {

	/**
	 * @param Request       $request
	 * @param AC\ListScreen $list_screen
	 *
	 * @return TableRows|false
	 */
	public static function create( Request $request, AC\ListScreen $list_screen ) {
		switch ( true ) {
			case $list_screen instanceof Post:
				return new TableRows\Post( $request, $list_screen );
			case $list_screen instanceof Media:
				return new TableRows\Media( $request, $list_screen );
			case $list_screen instanceof User:
				return new TableRows\User( $request, $list_screen );
			case $list_screen instanceof Taxonomy:
				return new TableRows\Taxonomy( $request, $list_screen );
			case $list_screen instanceof Comment:
				return new TableRows\Comment( $request, $list_screen );
		}

		return false;
	}

}