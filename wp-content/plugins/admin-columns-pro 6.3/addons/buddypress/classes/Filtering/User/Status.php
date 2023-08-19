<?php

namespace ACA\BP\Filtering\User;

use ACP;
use WP_Query;

class Status extends ACP\Filtering\Model {

	/**
	 * @param $query
	 *
	 * @return WP_Query
	 */
	public function filter_by_user_status( $query ) {
		global $wpdb;

		$query->query_where .= ' ' . $wpdb->prepare( "AND {$wpdb->users}.user_status = %s", $this->get_filter_value() );

		return $query;
	}

	/**
	 * @param array $vars
	 *
	 * @return array
	 */
	public function get_filtering_vars( $vars ) {
		add_filter( 'pre_user_query', [ $this, 'filter_by_user_status' ] );

		return $vars;
	}

	/**
	 * @return array
	 */
	public function get_filtering_data() {
		return [
			'options' => [
				0 => __( 'Active', 'buddypress' ),
				1 => __( 'Spammer', 'buddypress' ),
			],
		];
	}

}