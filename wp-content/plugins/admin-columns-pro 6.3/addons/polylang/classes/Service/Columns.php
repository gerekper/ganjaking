<?php

namespace ACA\Polylang\Service;

use AC;
use AC\Registerable;
use ACA\Polylang\Column;
use ACP\ListScreen;

class Columns implements Registerable {

	const GROUP_NAME = 'polylang';

	public function register(): void
    {
		add_action( 'ac/column_types', [ $this, 'add_columns' ] );
		add_action( 'ac/column_groups', [ $this, 'register_column_groups' ] );
	}

	public function register_column_groups( AC\Groups $groups ): void {
		$groups->add( self::GROUP_NAME, 'Polylang', 25 );
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