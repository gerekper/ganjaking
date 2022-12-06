<?php

namespace ACA\Types\Column;

use ACA\Types\Column;

class Taxonomy extends Column {

	protected function get_type_name() {
		return 'wpcf-termmeta';
	}

	public function get_fields() {
		$options = wp_cache_get( 'types_fields', 'aca_types_taxonomy' );

		if ( ! $options ) {
			$groups = get_posts( [
				'post_type' => 'wp-types-term-group',
				'fields'    => 'ids',
			] );

			$fields = [];
			$options = [];

			foreach ( $groups as $id ) {
				$fields = array_merge( $fields, wpcf_admin_fields_get_fields_by_group( $id, 'slug', true, false, true, TYPES_TERM_META_FIELD_GROUP_CPT_NAME, 'wpcf-termmeta' ) );
			}

			foreach ( $fields as $field ) {
				$options[ $field['id'] ] = $field['name'];
			}

			wp_cache_set( 'types_fields', $options, 'aca_types_taxonomy' );
		}

		return $options;
	}

	public function get_render_value( $id ) {
		return types_render_termmeta( $this->get_type_field_id(), [ 'term_id' => $id, 'separator' => ', ' ] );
	}

}