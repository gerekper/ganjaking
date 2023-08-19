<?php

namespace ACA\Types\Service;

use AC;
use ACA\Types\Column;
use ACP;

final class Columns implements AC\Registerable {

	public function register(): void
    {
		add_action( 'ac/column_groups', [ $this, 'register_column_groups' ] );
		add_action( 'acp/column_types', [ $this, 'register_columns' ] );
	}

	public function register_columns( AC\ListScreen $list_screen ) {

		switch ( true ) {

			// Post and Media
			case $list_screen instanceof AC\ListScreenPost :
				$list_screen->register_column_type( new Column\Post );
				$list_screen->register_column_type( new Column\Post\Intermediary() );
				$list_screen->register_column_type( new Column\Post\Relationship() );

				break;
			case $list_screen instanceof AC\ListScreen\User :
				$list_screen->register_column_type( new Column\User );

				break;
			case $list_screen instanceof ACP\ListScreen\Taxonomy :
				$list_screen->register_column_type( new Column\Taxonomy );

				break;
		}
	}

	public function register_column_groups( AC\Groups $groups ) {
		$groups->add( 'types', 'Toolset Types', 11 );
	}

}