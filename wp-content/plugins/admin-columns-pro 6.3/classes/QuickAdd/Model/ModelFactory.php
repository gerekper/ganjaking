<?php

namespace ACP\QuickAdd\Model;

use AC\ListScreen;

interface ModelFactory {

	/**
	 * @param ListScreen $list_screen
	 *
	 * @return Create
	 */
	public function create( ListScreen $list_screen );

}