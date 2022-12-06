<?php

namespace ACP\Export;

use AC;
use AC\Registerable;
use ACP\Export\HideOnScreen;
use ACP\Settings\ListScreen\HideOnScreenCollection;

class Admin implements Registerable {

	public function register() {
		add_action( 'acp/admin/settings/hide_on_screen', [ $this, 'add_hide_on_screen' ], 10, 2 );
	}

	public function add_hide_on_screen( HideOnScreenCollection $collection, AC\ListScreen $list_screen ) {
		if ( $list_screen instanceof ListScreen ) {
			$collection->add( new HideOnScreen\Export(), 50 );
		}
	}

}