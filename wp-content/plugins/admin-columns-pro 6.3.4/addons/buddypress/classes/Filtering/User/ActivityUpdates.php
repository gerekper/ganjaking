<?php

namespace ACA\BP\Filtering\User;

use ACA\BP\Column;
use ACP;
use WP_User_Query;

/**
 * @property Column\User\ActivityUpdates $column
 */
class ActivityUpdates extends ACP\Filtering\Model {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'numeric' );
		$this->set_ranged( true );
	}

	public function get_filtering_data() {
		return [];
	}

	/**
	 * @param $query WP_User_Query
	 */
	public function filter_by_activity_update( $query ) {
		$value = $this->get_filter_value();

		if ( empty( $value['min'] ) && empty( $value['max'] ) ) {
			return;
		}

		global $wpdb, $bp;

		// Sub query
		$having = '';
		$where = '';

		$min = (int) $value['min'];
		if ( $min ) {
			$having .= $wpdb->prepare( ' AND activities >= %d', $min );
		}

		$max = (int) $value['max'];

		if ( $max ) {
			$having .= $wpdb->prepare( ' AND activities <= %d', $max );
		}

		$activity = $this->column->get_activity_type();

		if ( $activity ) {
			$where = $wpdb->prepare( 'WHERE type = %s', $activity );
		}

		$sub_query = "
			SELECT user_id, COUNT(user_id) as activities
			FROM {$bp->activity->table_name}
			{$where}
			GROUP BY user_id
			HAVING activities != ''
			{$having}
			ORDER BY activities
			";

		$query->query_from .= ', (' . $sub_query . ') AS activity_users';
		$query->query_where .= " AND {$wpdb->users}.id = activity_users.user_id";
	}

	public function get_filtering_vars( $vars ) {
		add_action( 'pre_user_query', [ $this, 'filter_by_activity_update' ] );

		return $vars;
	}

}