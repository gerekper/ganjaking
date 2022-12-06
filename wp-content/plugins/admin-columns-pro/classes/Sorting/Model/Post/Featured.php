<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;

abstract class Featured extends AbstractModel {

	/**
	 * @return int[]
	 */
	abstract protected function get_featured_ids();

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function sorting_clauses_callback( $clauses ) {
		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$featured_ids = $this->get_featured_ids();

		if ( $featured_ids ) {
			$ids = implode( ",", array_map( 'intval', $featured_ids ) );

			$clauses['fields'] .= sprintf( ", $wpdb->posts.ID IN ( %s ) AS acsort_featured", $ids );
			$clauses['groupby'] = "$wpdb->posts.ID";
			$clauses['orderby'] = sprintf( "acsort_featured %s, $wpdb->posts.post_date", esc_sql( $this->get_order() ) );
		}

		return $clauses;
	}

}