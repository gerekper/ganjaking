<?php

namespace ACP\Filtering\Model\Media;

use ACP\Filtering\Model;

class MimeType extends Model {

	public function filter_by_mime_type( $where ) {
		global $wpdb;

		return $where . $wpdb->prepare( "AND {$wpdb->posts}.post_mime_type = %s", $this->get_filter_value() );
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_where', [ $this, 'filter_by_mime_type' ] );

		return $vars;
	}

	public function get_filtering_data() {
		$data = [];
		$mime_types = array_flip( wp_get_mime_types() );

		foreach ( $this->strategy->get_values_by_db_field( 'post_mime_type' ) as $_value ) {
			$data['options'][ $_value ] = isset( $mime_types[ $_value ] ) ? $mime_types[ $_value ] : $_value;
		}

		return $data;
	}

}