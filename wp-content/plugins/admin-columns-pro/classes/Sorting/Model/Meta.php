<?php

namespace ACP\Sorting\Model;

use AC;
use ACP;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Type\DataType;

/**
 * @deprecated 5.2
 */
class Meta extends AbstractModel {

	/**
	 * @var string
	 */
	protected $meta_key;

	public function __construct( AC\Column\Meta $column, DataType $data_type = null ) {
		parent::__construct( $data_type );

		$this->meta_key = $column->get_meta_key();
	}

	/**
	 * Get args for a WP_Meta_Query to sort on a single key
	 * @return array Arguments to sort with using a WP_Meta_Query
	 * @since 4.0
	 * @see   \WP_Meta_Query
	 */
	public function get_sorting_vars() {
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

		return $vars;
	}

	/**
	 * @param string $data_type_value
	 *
	 * @return $this
	 * @deprecated 5.2
	 */
	public function set_data_type( $data_type_value ) {
		$this->data_type = new DataType( $data_type_value );

		return $this;
	}

}