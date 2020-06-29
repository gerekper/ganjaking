<?php

namespace ACP\Sorting;

use AC;

class NativeSortables {

	const OPTIONS_KEY = 'ac_sorting';

	private $list_screen;

	public function __construct( AC\ListScreen $list_screen ) {
		$this->list_screen = $list_screen;
	}

	private function get_key() {
		return self::OPTIONS_KEY . '_' . $this->list_screen->get_key() . "_default";
	}

	public function store( $data ) {
		update_option( $this->get_key(), $data, false );
	}

	public function get() {
		return get_option( $this->get_key() );
	}

	/**
	 * @param $order_by
	 *
	 * @return string Column name
	 */
	public function is_sortable( $order_by ) {

		// The format is: 'internal-name' => 'orderby' or 'internal-name' => [ 'orderby', true ]
		// The second format will make the initial sorting order be descending
		$data = $this->get();

		if ( $data ) {

			foreach ( $data as $column_name => $_order_by ) {

				if ( is_string( $_order_by ) && $_order_by === $order_by ) {
					return $column_name;
				}

				if ( is_array( $_order_by ) && $_order_by[0] === $order_by ) {
					return $column_name;
				}
			}
		}

		return false;
	}

}