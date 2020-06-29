<?php

namespace ACP\Sorting\Table;

use AC;
use ACP\Sorting\NativeSortableRepository;

class Sorted {

	/**
	 * @var AC\ListScreen
	 */
	private $list_screen;

	/**
	 * @var string
	 */
	private $order;

	/**
	 * @var string
	 */
	private $order_by;

	/**
	 * @var array
	 */
	private $request;

	/**
	 * @var Preference
	 */
	private $preference;

	/**
	 * @var NativeSortableRepository
	 */
	private $native_sortables;

	public function __construct(
		AC\ListScreen $list_screen,
		Preference $preference,
		NativeSortableRepository $native_sortables,
		array $request_var = []
	) {
		$this->list_screen = $list_screen;
		$this->preference = $preference;
		$this->request = $request_var;
		$this->native_sortables = $native_sortables;

		$this->load();
	}

	private function get_default_sorting() {
		return new DefaultSorted( $this->list_screen );
	}

	private function load() {
		$default = $this->get_default_sorting();

		// Preference by setting
		if ( $default->exists() ) {
			$this->set_order_by( $default->get_order_by() );
			$this->set_order( $default->get_order() );
		}

		// Preference by user
		if ( $this->preference->get_order_by() ) {
			$this->set_order_by( $this->preference->get_order_by() );
			$this->set_order( $this->preference->get_order() );
		}

		// Ajax
		if ( $this->request ) {
			if ( array_key_exists( 'orderby', $this->request ) ) {
				$this->set_order_by( $this->request['orderby'] );
			}
			if ( array_key_exists( 'order', $this->request ) ) {
				$this->set_order( $this->request['order'] );
			}
		}
	}

	/**
	 * @return bool
	 */
	public function is_sorted_default() {
		$default = $this->get_default_sorting();

		return $default->exists() && $this->order_by === $default->get_order_by() && $this->order === $default->get_order();
	}

	public function set_order( $order ) {
		$this->order = strtolower( $order ) === 'desc' ? 'desc' : 'asc';
	}

	public function set_order_by( $order_by ) {
		$this->order_by = $order_by;
	}

	public function get_order() {
		return $this->order;
	}

	public function get_order_by() {
		return $this->order_by;
	}

	/**
	 * @return AC\Column|false
	 */
	public function get_column() {
		$column_name = $this->native_sortables->get_sortable_column_by_order_by( $this->list_screen->get_key(), $this->get_order_by() );

		if ( ! $column_name ) {
			$column_name = $this->get_order_by();
		}

		return $this->list_screen->get_column_by_name( $column_name );
	}

}