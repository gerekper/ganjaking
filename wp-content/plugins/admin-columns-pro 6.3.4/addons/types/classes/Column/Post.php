<?php

namespace ACA\Types\Column;

use ACA\Types\Column;

class Post extends Column {

	protected function get_group_name() {
		return $this->get_post_type();
	}

	public function get_fields() {
		$fields = wp_cache_get( 'types_fields', 'aca_types' );

		if ( ! $fields ) {

			$fields = [];

			$group_ids = apply_filters(
				'types_filter_get_field_group_ids_by_post_type', [], $this->get_post_type()
			);

			foreach ( $group_ids as $group_id ) {
				$field_definitions_for_group = apply_filters(
					'types_filter_query_field_definitions', [], [
					'domain'   => 'posts',
					'group_id' => (int) $group_id,
				] );

				if ( ! $field_definitions_for_group ) {
					continue;
				}

				$group_options = [];

				foreach ( $field_definitions_for_group as $field ) {
					$group_options[ $field['id'] ] = $field['name'];
				}

				$fields[ $group_id ] = [
					'title'   => get_the_title( $group_id ),
					'options' => $group_options,
				];

			}
			asort( $fields );

			wp_cache_set( 'types_fields', $fields, 'aca_types' );
		}

		return $fields;
	}

	public function get_render_value( $id ) {
		return types_render_field( $this->get_type_field_id(), [ 'separator' => ', ', 'id' => $id ] );
	}

}