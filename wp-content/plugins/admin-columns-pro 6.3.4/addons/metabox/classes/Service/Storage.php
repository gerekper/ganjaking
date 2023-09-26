<?php

namespace ACA\MetaBox\Service;

use AC\Registerable;

final class Storage implements Registerable {

	public function register(): void
    {
		add_filter( 'rwmb_meta_box_settings', [ $this, 'set_storage_table_to_field' ] );
	}

	public function set_storage_table_to_field( $meta_box ) {
		if ( isset( $meta_box['storage_type'], $meta_box['table'] ) && 'custom_table' === $meta_box['storage_type'] ) {
			foreach ( $meta_box['fields'] as &$field ) {
				$field['ac_storage_table'] = $meta_box['table'];
			}
		}

		return $meta_box;
	}

}