<?php

namespace ACP\QuickAdd\Model;

use AC\ListScreen;

class Factory {

	/**
	 * @param ListScreen $list_screen
	 *
	 * @return Create|null
	 */
	public static function create( ListScreen $list_screen ) {
		if ( $list_screen instanceof ListScreen\Post ) {
			return new Create\Post( $list_screen->get_post_type() );
		}

		return null;
	}

}