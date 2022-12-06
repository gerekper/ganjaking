<?php

namespace ACA\ACF;

use AC;

class ColumnGroup implements AC\Registerable {

	const SLUG = 'acf';

	public function register() {
		add_action( 'ac/column_groups', [ $this, 'register_column_groups' ] );
	}

	public function register_column_groups( AC\Groups $groups ) {
		$groups->register_group( self::SLUG, 'Advanced Custom Fields', 11 );
	}

}