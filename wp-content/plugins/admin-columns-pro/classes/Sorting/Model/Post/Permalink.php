<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Sorter;
use ACP\Sorting\Strategy;

/**
 * @property Strategy\Post $strategy
 */
class Permalink extends AbstractModel {

	public function get_sorting_vars() {
		return [
			'ids' => $this->get_sorted_ids(),
		];
	}

	/**
	 * @return int[]
	 */
	private function get_sorted_ids() {
		global $wpdb;

		// only fetch the fields needed for `get_permalink()`
		$sql = $wpdb->prepare( "
			SELECT pp.ID, pp.post_type, pp.post_status, pp.post_name, pp.post_date, pp.post_parent
			FROM {$wpdb->posts} AS pp 
			WHERE pp.post_type = %s
		",
			$this->strategy->get_post_type()
		);

		$status = $this->strategy->get_post_status();

		if ( $status ) {
			$sql .= sprintf( " AND pp.post_status IN ( '%s' )", implode( "','", array_map( 'esc_sql', $status ) ) );
		}

		$results = $wpdb->get_results( $sql );

		if ( ! $results ) {
			return [];
		}

		$ids = [];

		foreach ( $results as $object ) {
			$ids[ $object->ID ] = get_permalink( get_post( $object ) );
		}

		return ( new Sorter() )->sort( $ids, $this->get_order() );
	}

}