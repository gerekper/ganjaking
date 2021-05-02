<?php

namespace ACP\Editing\Ajax;

use AC;

interface EditableRowsFactoryInterface {

	/**
	 * @param AC\Request    $request
	 * @param AC\ListScreen $list_screen
	 *
	 * @return EditableRows
	 */
	public static function create( AC\Request $request, AC\ListScreen $list_screen );

}