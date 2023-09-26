<?php

namespace ACA\ACF\Service;

use AC\Column;
use AC\Registerable;

class RemoveDeprecatedColumnFromTypeSelector implements Registerable {

	public function register(): void
    {
		add_filter( 'ac/column/settings/column_types', [ $this, 'remove_from_type_selector' ], 10, 2 );
	}

	public function remove_from_type_selector( array $column_types, Column $column ) {

		// Only show the column type when the current selected type is itself
		if ( $column->get_type() !== 'column-acf_field' ) {
			unset( $column_types['column-acf_field'] );
		}

		return $column_types;
	}

}