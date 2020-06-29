<?php

namespace ACP\Filtering\Strategy;

use ACP\Filtering\Strategy;
use WP_User_Query;

class User extends Strategy {

	public function handle_request() {
		add_action( 'pre_get_users', [ $this, 'handle_filter_requests' ], 1 );
	}

	/**
	 * Handle filter request
	 *
	 * @param WP_User_Query $user_query
	 *
	 * @since 3.5
	 */
	public function handle_filter_requests( $user_query ) {
		if ( ! isset( $_GET['acp_filter_action'] ) ) {
			return;
		}

		$user_query->query_vars = $this->model->get_filtering_vars( $user_query->query_vars );
	}

	public function get_values_by_db_field( $user_field ) {
		global $wpdb;

		$user_field = sanitize_key( $user_field );

		$values = $wpdb->get_col( "
			SELECT DISTINCT {$user_field}
			FROM {$wpdb->users}
			WHERE {$user_field} <> ''
			ORDER BY 1
		" );

		if ( ! $values || is_wp_error( $values ) ) {
			return [];
		}

		return $values;
	}

}