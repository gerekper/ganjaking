<?php

namespace ACA\BbPress\Service;

use AC;
use AC\Registerable;
use ACA\BbPress\Column;

class Columns implements Registerable {

	public function register(): void
    {
		add_action( 'ac/column_groups', [ $this, 'register_column_group' ] );
	}

	/**
	 * @param AC\Groups $groups
	 */
	public function register_column_group( AC\Groups $groups ) {
		$groups->add( 'bbpress', __( 'bbPress' ), 25 );
	}

}