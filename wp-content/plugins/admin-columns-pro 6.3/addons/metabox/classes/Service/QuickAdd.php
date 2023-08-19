<?php

namespace ACA\MetaBox\Service;

use AC;
use AC\Registerable;

final class QuickAdd implements Registerable {

	public function register(): void
    {
		add_filter( 'acp/quick_add/enable', [ $this, 'disable_quick_add' ], 10, 2 );
	}

	public function disable_quick_add( $enabled, AC\ListScreen $list_screen ) {
		if ( $list_screen instanceof AC\ListScreen\Post && in_array( $list_screen->get_post_type(), [ 'meta-box', 'mb-post-type', 'mb-taxonomy', 'mb-views' ] ) ) {
			$enabled = false;
		}

		return $enabled;
	}

}