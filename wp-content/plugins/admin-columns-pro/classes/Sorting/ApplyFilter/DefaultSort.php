<?php

namespace ACP\Sorting\ApplyFilter;

use AC\ApplyFilter;
use AC\ListScreen;
use ACP\Sorting\Type\SortType;

/**
 * Wrapper class for the 'acp/sorting/default' filter
 */
class DefaultSort implements ApplyFilter {

	/**
	 * @var ListScreen
	 */
	private $list_screen;

	/**
	 * @param ListScreen $list_screen
	 */
	public function __construct( ListScreen $list_screen ) {
		$this->list_screen = $list_screen;
	}

	/**
	 * @param SortType|null $sort_type
	 *
	 * @return SortType|null
	 */
	public function apply_filters( $sort_type ) {
		$args = $sort_type instanceof SortType
			? $this->create_args( $sort_type )
			: [];

		/**
		 * @param array $args [ 0 => (string) $column_name, 1 => (bool) $descending ]
		 * @param ListScreen
		 */
		$args = apply_filters( 'acp/sorting/default', $args, $this->list_screen );

		$order_by = $this->parse_order_by( $args );

		if ( ! $order_by ) {
			return null;
		}

		return new SortType(
			$order_by,
			$this->parse_order( $args )
		);
	}

	private function create_args( SortType $sort_type ) {
		return [
			$sort_type->get_order_by(),
			'desc' === $sort_type->get_order(),
		];
	}

	/**
	 * @param mixed $args
	 *
	 * @return string|null
	 */
	private function parse_order_by( $args ) {
		return $args && is_array( $args ) && isset( $args[0] ) && is_string( $args[0] ) && $args[0]
			? $args[0]
			: null;
	}

	/**
	 * @param mixed args
	 *
	 * @return string
	 */
	private function parse_order( $args ) {
		return is_array( $args ) && isset( $args[1] ) && $args[1]
			? 'desc'
			: 'asc';
	}

}