<?php

namespace ACP\Export;

use AC;
use AC\Registerable;
use ACP\Settings\ListScreen\HideOnScreenCollection;
use ACP\Type\HideOnScreen\Group;

class Admin implements Registerable {

	public function register(): void
    {
		add_action( 'acp/admin/settings/hide_on_screen', [ $this, 'add_hide_on_screen' ], 10, 2 );
	}

	public function add_hide_on_screen( HideOnScreenCollection $collection, AC\ListScreen $list_screen ) {
		if ( $list_screen instanceof ListScreen ) {
			$collection->add( new HideOnScreen\Export(), new Group( Group::FEATURE ), 50 );
		}
	}

}