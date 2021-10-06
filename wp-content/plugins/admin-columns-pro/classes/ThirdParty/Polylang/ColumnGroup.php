<?php

namespace ACP\ThirdParty\Polylang;

use AC;
use AC\Registrable;

class ColumnGroup implements Registrable {

	const NAME = 'polylang';

	public function register() {
		add_action( 'ac/column_groups', [ $this, 'register_column_groups' ] );
	}

	public function register_column_groups( AC\Groups $groups ) {
		$groups->register_group( 'polylang', 'Polylang', 25 );
	}

}