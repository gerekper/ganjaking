<?php

namespace ACP\Service;

use AC\Registrable;

class Banner implements Registrable {

	public function register() {
		add_filter( 'ac/show_banner', '__return_false' );
	}

}