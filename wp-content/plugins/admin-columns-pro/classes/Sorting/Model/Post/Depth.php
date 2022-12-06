<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Strategy\Post;
use ACP\Sorting\Type\DataType;

/**
 * @property Post $strategy
 */
class Depth extends AbstractModel {

	public function __construct() {
		parent::__construct( new DataType( DataType::NUMERIC ) );
	}

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

	private function get_depth( $id, array $ids, $depth = 0 ) {
		if ( ! array_key_exists( $id, $ids ) ) {
			return $depth;
		}

		// prevents infinite loop
		if ( $depth > 99 ) {
			return $depth;
		}

		$parent = $ids[ $id ];

		return 0 === $parent
			? $depth
			: $this->get_depth( $parent, $ids, ++$depth );
	}

	private function get_sorted_ids() {
		$post_status = $this->strategy->get_post_status();

		$ids = get_posts( [
			'fields'         => 'id=>parent',
			'post_type'      => $this->strategy->get_post_type(),
			'post_status'    => $post_status ?: 'any',
			'posts_per_page' => -1,
		] );

		$values = [];

		foreach ( array_keys( $ids ) as $id ) {
			$values[ $id ] = $this->get_depth( $id, $ids );
		}

		asort( $values, SORT_NUMERIC );

		return array_keys( $values );
	}

}