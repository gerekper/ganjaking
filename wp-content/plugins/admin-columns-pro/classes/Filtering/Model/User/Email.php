<?php

namespace ACP\Filtering\Model\User;

use ACP\Filtering\Model;
use WP_Query;

class Email extends Model {

	/**
	 * @param $query
	 *
	 * @return WP_Query
	 */
	public function filter_by_email( $query ) {
		global $wpdb;

		$query->query_where .= ' ' . $wpdb->prepare( "AND {$wpdb->users}.user_email = %s", $this->get_filter_value() );

		return $query;
	}

	/**
	 * @param array $vars
	 *
	 * @return array
	 */
	public function get_filtering_vars( $vars ) {
		add_filter( 'pre_user_query', [ $this, 'filter_by_email' ] );

		return $vars;
	}

	/**
	 * @return array
	 */
	public function get_filtering_data() {
		$data = [];

		if ( $values = $this->strategy->get_values_by_db_field( 'user_email' ) ) {
			foreach ( $values as $value ) {
				$data['options'][ $value ] = $value;
			}
		}

		return $data;
	}

}