<?php

namespace ACA\Pods\Service;

use AC;
use AC\Registerable;
use ACA\Pods\Column;
use ACP;

final class Columns implements Registerable {

	public function register(): void
    {
		add_action( 'ac/column_groups', [ $this, 'register_column_groups' ] );
		add_action( 'acp/column_types', [ $this, 'add_columns' ] );
	}

	/**
	 * @param AC\Groups $groups
	 */
	public function register_column_groups( $groups ) {
		$groups->add( 'pods', 'Pods', 11 );

	}

	/**
	 * Add custom columns
	 *
	 * @param AC\ListScreen $list_screen
	 */
	public function add_columns( AC\ListScreen $list_screen ) {

		switch ( true ) {

			case $list_screen instanceof AC\ListScreen\Comment :
				$list_screen->register_column_type( new Column\Comment );

				break;
			case $list_screen instanceof AC\ListScreen\Post :
				$list_screen->register_column_type( new Column\Post );

				break;
			case $list_screen instanceof AC\ListScreen\Media :
				$list_screen->register_column_type( new Column\Media );

				break;
			case $list_screen instanceof AC\ListScreen\User :
				$list_screen->register_column_type( new Column\User );

				break;
			case $list_screen instanceof ACP\ListScreen\Taxonomy :
				$list_screen->register_column_type( new Column\Taxonomy() );

				break;
		}
	}

}