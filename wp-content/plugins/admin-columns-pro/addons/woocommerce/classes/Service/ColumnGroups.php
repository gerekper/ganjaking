<?php

namespace ACA\WC\Service;

use AC\Groups;
use AC\Registerable;

class ColumnGroups implements Registerable {

	public function register() {
		add_action( 'ac/column_groups', [ $this, 'register_column_groups' ] );
	}

	public function register_column_groups( Groups $groups ) {
		$groups->register_group( 'woocommerce', __( 'WooCommerce', 'codepress-admin-columns' ), 15 );
	}

}