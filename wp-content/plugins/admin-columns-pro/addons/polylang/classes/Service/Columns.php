<?php

namespace ACA\Polylang\Service;

use AC;
use AC\Registerable;
use ACP\ListScreen;
use ACA\Polylang\Column;

class Columns implements Registerable {

	const GROUP_NAME = 'polylang';

	public function register() {
		add_action( 'ac/column_types', [ $this, 'add_columns' ] );
		add_action( 'ac/column_groups', [ $this, 'register_column_groups' ] );
	}

	public function register_column_groups( AC\Groups $groups ): void {
		$groups->register_group( self::GROUP_NAME, 'Polylang', 25 );
	}

	public function add_columns( AC\ListScreen $list_screen ): void {
		if (
			$list_screen instanceof ListScreen\Post ||
			$list_screen instanceof ListScreen\Taxonomy ||
			$list_screen instanceof ListScreen\Media
		) {
			$list_screen->register_column_type( new Column\Language() );
		}
	}

}