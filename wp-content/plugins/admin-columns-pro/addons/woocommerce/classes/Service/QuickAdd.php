<?php

namespace ACA\WC\Service;

use AC\ListScreen;
use AC\Registerable;
use ACA\WC\ListScreen\ShopOrder;

class QuickAdd implements Registerable {

	public function register() {
		add_filter( 'acp/quick_add/enable', [ $this, 'disable_quick_add' ], 10, 2 );
	}

	public function disable_quick_add( $enabled, ListScreen $list_screen ) {
		if ( $list_screen instanceof ShopOrder ) {
			$enabled = false;
		}

		return $enabled;
	}

}