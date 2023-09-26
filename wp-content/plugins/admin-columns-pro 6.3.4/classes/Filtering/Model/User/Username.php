<?php

namespace ACP\Filtering\Model\User;

use ACP\Filtering\Model;

class Username extends Model {

	public function filter_by_username( $query ) {
		global $wpdb;

		$query->query_where .= ' ' . $wpdb->prepare( "AND {$wpdb->users}.user_login = %s", $this->get_filter_value() );

		return $query;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'pre_user_query', [ $this, 'filter_by_username' ] );

		return $vars;
	}

	public function get_filtering_data() {
		$data = [];

		if ( $values = $this->strategy->get_values_by_db_field( 'user_login' ) ) {
			foreach ( $values as $value ) {
				$data['options'][ $value ] = $value;
			}
		}

		return $data;
	}

}