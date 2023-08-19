<?php

namespace ACA\BP\Service;

use AC;
use AC\Registerable;
use ACA\BP\Editing\Ajax\TableRows;
use ACA\BP\ListScreen\Email;
use ACA\BP\ListScreen\Group;

class Table implements Registerable {

	/**
	 * @var AC\Asset\Location\Absolute
	 */
	private $location;

	/**
	 * @param AC\Asset\Location\Absolute $location
	 */
	public function __construct( AC\Asset\Location\Absolute $location ) {
		$this->location = $location;
	}

	public function register(): void
    {
		add_action( 'ac/table/list_screen', [ $this, 'init_editable_table' ] );
		add_action( 'ac/table_scripts', [ $this, 'table_scripts' ], 1 );
	}

	private function is_bp_list_screen( $list_screen ) {
		return $list_screen instanceof Group ||
		       $list_screen instanceof Email;
	}

	/**
	 * @param AC\ListScreen $list_screen
	 */
	public function table_scripts( AC\ListScreen $list_screen ) {
		if ( ! $this->is_bp_list_screen( $list_screen ) ) {
			return;
		}

		$style = new AC\Asset\Style( 'aca-bp-table', $this->location->with_suffix( 'assets/css/table.css' ) );
		$style->enqueue();
	}

	/**
	 * @param AC\ListScreen $list_screen
	 */
	public function init_editable_table( AC\ListScreen $list_screen ) {
		if ( ! $list_screen instanceof Group ) {
			return;
		}

		$table_rows = new TableRows\Groups( new AC\Request(), $list_screen );

		if ( $table_rows->is_request() ) {
			$table_rows->register();
		}
	}

}