<?php

namespace ACP\QuickAdd\Table\Preference;

use AC\Preferences\Site;

class ShowButton extends Site {

	const NAME = 'show_new_inline_button';

	public function __construct( $user_id = null ) {
		parent::__construct( self::NAME, $user_id );
	}

	public function is_active( $key ) {
		return in_array( $this->get( $key ), [ true, null ], true );
	}

}