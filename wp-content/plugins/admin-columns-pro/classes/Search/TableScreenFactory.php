<?php

namespace ACP\Search;

use AC\ListScreen;
use AC\ListScreen\Comment;
use AC\ListScreen\Media;
use AC\ListScreen\Post;
use AC\ListScreen\User;
use AC\Request;
use ACP\ListScreen\Taxonomy;

class TableScreenFactory {

	/**
	 * @param Addon      $addon
	 * @param ListScreen $list_screen
	 * @param Request    $request
	 * @param array      $assets
	 *
	 * @return TableScreen|false
	 */
	public static function create( Addon $addon, ListScreen $list_screen, Request $request, array $assets ) {
		switch ( true ) {
			case $list_screen instanceof Post :
			case $list_screen instanceof Media :
				return new TableScreen\Post( $addon, $list_screen, $request, $assets );
			case $list_screen instanceof Comment :
				return new TableScreen\Comment( $addon, $list_screen, $request, $assets );
			case $list_screen instanceof User :
				return new TableScreen\User( $addon, $list_screen, $request, $assets );
			case $list_screen instanceof Taxonomy :
				return new TableScreen\Taxonomy( $addon, $list_screen, $request, $assets );
		}

		return false;
	}

}