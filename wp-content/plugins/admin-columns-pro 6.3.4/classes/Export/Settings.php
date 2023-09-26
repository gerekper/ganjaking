<?php

namespace ACP\Export;

use AC;
use AC\Asset\Style;
use AC\Registerable;

class Settings implements Registerable {

	/**
	 * @var AC\Asset\Location
	 */
	protected $location;

	public function __construct( AC\Asset\Location $location ) {
		$this->location = $location;
	}

	public function register(): void
    {
		add_action( 'ac/column/settings', [ $this, 'column_settings' ], 9 );
		add_action( 'ac/admin_scripts/columns', [ $this, 'admin_scripts' ] );
	}

	/**
	 * @param AC\Column $column
	 *
	 * @return bool
	 */
	private function is_exportable( AC\Column $column ) {
		$list_screen = $column->get_list_screen();

		if ( ! $list_screen instanceof ListScreen ) {
			return false;
		}

		if ( $column instanceof Exportable && ! $column->export() ) {
			return false;
		}

		return true;
	}

	public function column_settings( AC\Column $column ) {
		if ( $this->is_exportable( $column ) ) {
			$column->add_setting( new Settings\Column( $column ) );
		}
	}

	public function admin_scripts() {
		$style = new Style( 'acp-search-admin', $this->location->with_suffix( 'assets/search/css/admin.css' ) );
		$style->enqueue();
	}

}