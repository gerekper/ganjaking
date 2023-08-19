<?php

namespace ACA\EC\Filtering;

use ACP;
use WP_Query;

abstract class UpcomingEvent extends ACP\Filtering\Model {

	abstract protected function get_related_meta_key();

	/**
	 * @param string   $where
	 * @param WP_Query $query
	 *
	 * @return string
	 */
	public function filter_by_relation( $where, $query ) {
		if ( ! $query->is_main_query() ) {
			return $where;
		}

		$ids = implode( ',', array_map( 'absint', $this->get_related_post_ids() ) );
		$operator = $this->get_filter_value() === 'yes' ? 'IN' : 'NOT IN';

		$where .= ' AND wp_posts.ID ' . $operator . ' ( ' . $ids . ' )';

		return $where;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_where', [ $this, 'filter_by_relation' ], 10, 2 );

		return $vars;
	}

	public function get_related_post_ids() {
		global $wpdb;

		$upcoming_events = tribe_get_events( [
			'start_date'     => date( 'Y-m-d H:i:s' ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
		] );
		$upcoming_event_ids = implode( ',', array_map( 'absint', $upcoming_events ) );

		$sql = $wpdb->prepare( "SELECT DISTINCT( meta_value )
								FROM {$wpdb->postmeta}
								WHERE meta_key = %s AND post_id IN ( " . $upcoming_event_ids . ' )',
			$this->get_related_meta_key() );

		return $wpdb->get_col( $sql );
	}

	public function get_filtering_data() {
		return [
			'options' => [
				'yes' => __( 'Has Upcoming Event', 'codepress-admin-columns' ),
				'no'  => __( 'Has No Upcoming Event', 'codepress-admin-columns' ),
			],
		];
	}

}