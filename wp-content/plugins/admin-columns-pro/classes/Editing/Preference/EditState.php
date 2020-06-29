<?php

namespace ACP\Editing\Preference;

use AC\Preferences\Site;

class EditState extends Site {

	public function __construct() {
		parent::__construct( 'editability_state' );
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	public function is_active( $key ) {

		// '1' (string) is for backwards compatibility
		return in_array( $this->get( $key ), [ '1', 1 ], true );
	}

}