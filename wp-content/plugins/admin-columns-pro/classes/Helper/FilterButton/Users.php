<?php

namespace ACP\Helper\FilterButton;

use ACP\Helper\FilterButton;

final class Users extends FilterButton {

	public function register() {
		add_action( 'restrict_manage_users', $this->get_callback(), 2 );
	}

}