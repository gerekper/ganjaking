<?php

namespace ACP\Sorting\Table;

use AC\ListScreen;

class DefaultSorted {

	/** @var ListScreen */
	private $list_screen;

	/** @var string */
	private $order;

	/** @var string */
	private $order_by;

	public function __construct( ListScreen $list_screen ) {
		$this->list_screen = $list_screen;

		$this->init();
	}

	private function has_setting() {
		return ! empty( $this->list_screen->get_preference( 'sorting' ) ) && ! empty( $this->list_screen->get_preference( 'sorting_order' ) );
	}

	private function init() {
		$args = false;

		if ( $this->has_setting() ) {
			$args = [
				$this->list_screen->get_preference( 'sorting' ),
				'desc' === $this->list_screen->get_preference( 'sorting_order' ),
			];
		}

		/**
		 * @param array $args [ 0 => (string) $column_name, 1 => (bool) $descending ]
		 * @param ListScreen
		 */
		$args = apply_filters( 'acp/sorting/default', $args, $this->list_screen );

		if ( ! $args ) {
			return;
		}

		if ( is_array( $args ) && isset( $args[0] ) && is_string( $args[0] ) && $args[0] ) {
			$this->order_by = $args[0];
		}

		if ( is_string( $args ) && $args ) {
			$this->order_by = $args;
		}

		$this->order = is_array( $args ) && isset( $args[1] ) && $args[1] ? 'desc' : 'asc';
	}

	/**
	 * @return bool
	 */
	public function exists() {
		return $this->order && $this->order_by;
	}

	public function get_order() {
		return $this->order;
	}

	public function get_order_by() {
		return $this->order_by;
	}

}