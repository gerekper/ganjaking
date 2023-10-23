<?php
/**
 * @package Polylang-WC
 */

/**
 * Handles the language information displayed for orders hen using legacy orders
 *
 * @since 1.9
 */
class PLLWC_Admin_Orders_Legacy extends PLLWC_Admin_Orders {

	/**
	 * Removes the standard Polylang languages columns for the orders list table
	 * and replace them with one unique column.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function custom_columns() {
		$translated_order_types = $this->data_store->get_post_types( 'display' );

		foreach ( $translated_order_types as $translated_order_type ) {
			$class = PLL()->filters_columns;

			remove_filter( 'manage_edit-' . $translated_order_type . '_columns', array( $class, 'add_post_column' ), 100 );
			remove_action( 'manage_' . $translated_order_type . '_posts_custom_column', array( $class, 'post_column' ) );

			add_filter( 'manage_edit-' . $translated_order_type . '_columns', array( $this, 'add_order_column' ), 100 );
			add_action( 'manage_' . $translated_order_type . '_posts_custom_column', array( $this, 'order_column' ), 10, 2 );
		}
	}

	/**
	 * Displays the Languages metabox.
	 *
	 * @since 0.1
	 *
	 * @param WP_Post $order Order object.
	 * @return void
	 */
	public function order_language( $order ) {
		$this->display_language_metabox( $order->ID );
	}
}
