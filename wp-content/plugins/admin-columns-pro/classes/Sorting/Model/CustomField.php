<?php

namespace ACP\Sorting\Model;

use AC;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Sorter;
use ACP\Sorting\Type\DataType;

/**
 * @deprecated 5.2
 */
class CustomField extends AbstractModel {

	/**
	 * @var string
	 */
	protected $meta_type;

	/**
	 * @var string
	 */
	protected $meta_key;

	/**
	 * @var string
	 */
	protected $post_type;

	public function __construct( AC\Column\CustomField $column, DataType $data_type = null ) {
		parent::__construct( $data_type );

		$this->meta_type = $column->get_meta_type();
		$this->meta_key = $column->get_meta_key();
		$this->post_type = $column->get_post_type();
	}

	protected function format_value( $value ) {
		return maybe_unserialize( $value );
	}

	private function get_sort_ids() {
		$id = uniqid();
		$vars = [
			'meta_query' => [
				$id => [
					'key'     => $this->meta_key,
					'type'    => $this->data_type->get_value(),
					'value'   => '',
					'compare' => '!=',
				],
			],
			'orderby'    => $id,
		];

		if ( $this->show_empty ) {
			$vars['meta_query'] = [
				'relation' => 'OR',

				// $id indicates which $key should be used for sorting. wp_query will use the $key for sorting, and applies both
				// the EXISTS and NOT EXISTS compares. Without $id it will not work when sorting is used
				// in conjunction with filtering.
				$id        => [
					'key'     => $this->meta_key,
					'type'    => $this->data_type->get_value(),
					'compare' => 'EXISTS',
				],
				[
					'key'     => $this->meta_key,
					'compare' => 'NOT EXISTS',
				],
			];
		}

		return $this->strategy->get_results( $vars );
	}

	private function get_meta_values( array $ids ) {
		$query = new AC\Meta\QueryMeta( $this->meta_type, $this->meta_key, $this->post_type );
		$query->select( 'id, meta_value' )
		      ->where_in( $ids );

		if ( $this->show_empty ) {
			$query->left_join();
		}

		return $query->get();
	}

	public function get_sorting_vars() {
		$ids = $this->get_sort_ids();

		if ( empty( $ids ) ) {
			return [];
		}

		$values = [];

		foreach ( $this->get_meta_values( $ids ) as $object ) {
			$values[ $object->id ] = $this->format_value( $object->meta_value );
		}

		return [
			'ids' => ( new Sorter() )->sort( $values, $this->get_order(), $this->data_type, $this->show_empty ),
		];
	}

}