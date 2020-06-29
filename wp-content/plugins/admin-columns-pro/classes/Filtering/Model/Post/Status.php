<?php

namespace ACP\Filtering\Model\Post;

use ACP\Filtering\Model;

class Status extends Model {

	public function get_filtering_vars( $vars ) {
		$vars['post_status'] = $this->get_filter_value();

		return $vars;
	}

	public function get_filtering_data() {
		global $wp_post_statuses;

		$values = $this->strategy->get_values_by_db_field( 'post_status' );

		if ( ! $values ) {
			return [];
		}

		$data = [];

		foreach ( $values as $value ) {
			if ( isset( $wp_post_statuses[ $value ] ) ) {
				$status_object = $wp_post_statuses[ $value ];

				if ( $status_object->internal ) {
					continue;
				}

				$data['options'][ $value ] = esc_html( $status_object->label );
			}
		}

		return $data;
	}

}