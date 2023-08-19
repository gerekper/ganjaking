<?php

namespace ACP\Table;

use AC;
use AC\Registerable;

class PrimaryColumn implements Registerable {

	/**
	 * @var AC\ListScreen
	 */
	private $list_screen;

	public function register(): void
    {
		add_action( 'ac/table', [ $this, 'init' ] );
	}

	public function init( AC\Table\Screen $table_screen ) {
		$this->list_screen = $table_screen->get_list_screen();

		add_filter( 'list_table_primary_column', [ $this, 'set_primary_column' ], 20 );
	}

	function set_primary_column( $column_name ) {
		return $this->list_screen->get_preference( 'primary_column' ) ?: $column_name;
	}

}