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
		$value = $this->get( $key );

		if ( null === $value ) {
			$value = apply_filters( 'acp/editing/inline/button_default_state', false );
		}

		// '1' (string) is for backwards compatibility
		return in_array( $value, [ '1', 1, true ], true );
	}

}