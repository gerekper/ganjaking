<?php

namespace ACP\Helper\FilterButton;

use ACP\Helper\FilterButton;

final class Taxonomy extends FilterButton {

	public function register() {
		add_action( 'in_admin_footer', $this->get_callback(), 2 );
	}

}