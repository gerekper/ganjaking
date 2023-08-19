<?php

namespace ACA\EC\Settings;

use AC;

class NonPublicPostLink extends AC\Settings\Column\PostLink {

	protected function get_display_options() {
		return array_intersect_key( parent::get_display_options(), array_flip( [ '', 'edit_post' ] ) );
	}

}