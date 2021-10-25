<?php

namespace ACP\Sorting;

class NativeSortableRepository {

	const OPTIONS_KEY = 'ac_sorting';

	/**
	 * @param string $list_screen_key
	 *
	 * @return string
	 */
	private function get_option_name( $list_screen_key ) {
		return sprintf( "%s_%s_default", self::OPTIONS_KEY, $list_screen_key );
	}

	/**
	 * @param string $list_screen_key
	 * @param array  $columns
	 *
	 * @return void
	 */
	public function update( $list_screen_key, array $columns ) {
		update_option( $this->get_option_name( $list_screen_key ), $columns, false );
	}

	/**
	 * @param string $list_screen_key
	 *
	 * @return bool
	 */
	public function exists( $list_screen_key ) {
		return false !== get_option( $this->get_option_name( $list_screen_key ) );
	}

	/**
	 * @param string $list_screen_key
	 *
	 * @return array
	 */
	public function get( $list_screen_key ) {
		return get_option( $this->get_option_name( $list_screen_key ), [] );
	}

	/**
	 * @param string $list_screen_key
	 *
	 * @return void
	 */
	public function delete( $list_screen_key ) {
		delete_option( $this->get_option_name( $list_screen_key ) );
	}

	/**
	 * @param string $list_screen_key
	 * @param string $column_name
	 *
	 * @return bool
	 */
	public function is_column_sortable( $list_screen_key, $column_name ) {
		$data = $this->get( $list_screen_key );

		return $data && array_key_exists( $column_name, $data );
	}

	/**
	 * @param string $list_screen_key
	 * @param string $order_by
	 *
	 * @return string|null
	 */
	public function get_sortable_column_by_order_by( $list_screen_key, $order_by ) {

		// The format is: [ $column_name => $orderby ] or [ $column_name => [ $orderby, true ] ]
		// The second format will make the initial sorting order be descending
		$data = $this->get( $list_screen_key );

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

		return null;
	}

}