<?php

namespace ACP\Export;

use AC;
use AC\Registrable;
use ACP\Export\HideOnScreen;
use ACP\Settings\ListScreen\HideOnScreenCollection;

/**
 * Handles general functionality for admin screens
 * @since 1.0
 */
class Admin implements Registrable {

	public function register() {
		add_action( 'acp/admin/settings/hide_on_screen', [ $this, 'add_hide_on_screen' ], 10, 2 );
	}

	public function add_hide_on_screen( HideOnScreenCollection $collection, AC\ListScreen $list_screen ) {
		if ( $list_screen instanceof ListScreen ) {
			$collection->add( new HideOnScreen\Export(), 50 );
		}
	}

}