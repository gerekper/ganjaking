<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\Sorter;
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
		return [
			'ids' => ( new Sorter() )->sort( $this->get_sorted_ids(), $this->get_order(), $this->data_type, $this->show_empty ),
		];
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

		return $values;
	}

}