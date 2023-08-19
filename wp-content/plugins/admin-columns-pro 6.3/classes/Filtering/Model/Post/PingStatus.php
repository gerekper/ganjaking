<?php

namespace ACP\Filtering\Model\Post;

use ACP\Filtering\Model;

class PingStatus extends Model {

	public function filter_by_ping_status( $where ) {
		global $wpdb;

		return $where . $wpdb->prepare( "AND {$wpdb->posts}.ping_status = %s", $this->get_filter_value() );
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_where', [ $this, 'filter_by_ping_status' ] );

		return $vars;
	}

	public function get_filtering_data() {
		$data = [];

		if ( $values = $this->strategy->get_values_by_db_field( 'ping_status' ) ) {
			foreach ( $values as $value ) {
				$data['options'][ $value ] = ucfirst( $value );
			}
		}

		return $data;
	}

}