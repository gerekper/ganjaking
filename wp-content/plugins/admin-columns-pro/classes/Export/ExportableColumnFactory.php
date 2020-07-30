<?php

namespace ACP\Export;

use AC;
use AC\Column;

class ExportableColumnFactory {

	/**
	 * @var AC\ListScreen;
	 */
	private $list_screen;

	public function __construct( AC\ListScreen $list_screen ) {
		$this->list_screen = $list_screen;
	}

	/**
	 * @param array $exclude_columns
	 *
	 * @return Column[]
	 */
	public function create( $exclude_columns = [] ) {
		$columns = [];

		foreach ( $this->list_screen->get_columns() as $column ) {
			// Don't add columns that are not active
			if ( ! $this->is_active( $column ) ) {
				continue;
			}

			if ( in_array( $column->get_name(), $exclude_columns ) ) {
				continue;
			}

			if ( apply_filters( 'ac/export/column/disable', false, $column ) ) {
				continue;
			}

			$columns[] = $column;
		}

		return $columns;
	}

	private function is_active( Column $column ) {
		if ( $column instanceof Exportable && ! $column->is_valid() ) {
			return false;
		}

		/** @var Settings\Column $setting */
		$setting = $column->get_setting( 'export' );

		return $setting && $setting->is_active();
	}

}