<?php

namespace ACP\Search\Preferences;

use AC\ListScreen;
use AC\Preferences\Site;

class SmartFiltering extends Site {

	public function __construct() {
		parent::__construct( 'enable_smart_filtering' );
	}

	public function is_active( ListScreen $list_screen ) {
		$is_active = $this->get( $list_screen->get_key() );

		return 1 === $is_active || null === $is_active;
	}

}