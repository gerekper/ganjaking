<?php

namespace ACA\ACF\Service;

use AC;
use AC\Registerable;
use ACA\ACF\Column;
use ACA\ACF\Helper;

class ColumnSettings implements Registerable {

	public function register(): void
    {
		add_action( 'ac/column/settings', [ $this, 'add_edit_field_link_to_column_type' ] );
	}

	public function add_edit_field_link_to_column_type( AC\Column $column ) {
		if ( ! $column instanceof Column ) {
			return;
		}

		foreach ( $column->get_settings() as $setting ) {
			if ( ! $setting instanceof AC\Settings\Column\Type ) {
				continue;
			}

			$edit_field = ( new Helper() )->get_field_edit_link( $column->get_field_hash() );

			if ( ! $edit_field ) {
				continue;
			}

			$setting->set_read_more( $edit_field );
		}
	}

}