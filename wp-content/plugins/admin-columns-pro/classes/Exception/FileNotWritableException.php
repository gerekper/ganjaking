<?php

namespace ACP\Exception;

use AC\ListScreen;
use RuntimeException;

class FileNotWritableException extends RuntimeException {

	/**
	 * @param ListScreen $list_screen
	 *
	 * @return self
	 */
	public static function from_saving_list_screen( ListScreen $list_screen ) {
		return new self( sprintf( 'Failed to save ListScreen with layout %s to file.', $list_screen->get_layout_id() ) );
	}

	/**
	 * @param ListScreen $list_screen
	 *
	 * @return self
	 */
	public static function from_removing_list_screen( ListScreen $list_screen ) {
		return new self( sprintf( 'Failed to delete the file containing ListScreen with layout %s.', $list_screen->get_layout_id() ) );
	}

}