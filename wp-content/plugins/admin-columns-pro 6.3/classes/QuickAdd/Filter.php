<?php

namespace ACP\QuickAdd;

use AC\ListScreen;

class Filter {

	/**
	 * @param ListScreen $list_screen
	 *
	 * @return bool
	 */
	public function match( ListScreen $list_screen ) {
		return (bool) apply_filters( 'acp/quick_add/enable', true, $list_screen );
	}

}