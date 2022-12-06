<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\WarningAware;

class Permalink extends AbstractModel implements WarningAware {

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function sorting_clauses_callback( $clauses ) {
		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$clauses['orderby'] = SqlOrderByFactory::create_with_ids( "$wpdb->posts.ID", $this->get_sorted_ids(), $this->get_order() ) ?: $clauses['orderby'];

		return $clauses;
	}

	/**
	 * @return int[]
	 */
	private function get_sorted_ids() {
		global $wpdb;

		// only fetch the fields needed for `get_permalink()`
		$sql = $wpdb->prepare( "
			SELECT pp.ID, pp.post_type, pp.post_status, pp.post_name, pp.post_date, pp.post_parent
			FROM $wpdb->posts AS pp
			WHERE pp.post_type = %s AND pp.post_name <> ''
		",
			$this->strategy->get_post_type()
		);

		$status = $this->strategy->get_post_status();

		if ( $status ) {
			$sql .= sprintf( "\nAND pp.post_status IN ( '%s' )", implode( "','", array_map( 'esc_sql', $status ) ) );
		}

		$results = $wpdb->get_results( $sql );

		if ( ! $results ) {
			return [];
		}

		$values = [];

		foreach ( $results as $object ) {
			$link = get_permalink( get_post( $object ) );

			if ( $link && is_string( $link ) ) {
				$values[ $object->ID ] = $link;
			}
		}

		natcasesort( $values );

		return array_keys( $values );
	}

}