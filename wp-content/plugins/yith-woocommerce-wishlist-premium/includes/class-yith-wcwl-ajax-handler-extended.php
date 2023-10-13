<?php
/**
 * Static class that will handle all ajax calls for the list
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Ajax_Handler_Extended' ) ) {
	/**
	 * WooCommerce Wishlist Ajax Handler
	 *
	 * @since 3.0.0
	 */
	class YITH_WCWL_Ajax_Handler_Extended {
		/**
		 * Performs all required add_actions to handle forms
		 *
		 * @return void
		 */
		public static function init() {
			// handle ajax requests.
			add_action( 'wp_ajax_update_item_quantity', array( 'YITH_WCWL_Ajax_Handler_Extended', 'update_quantity' ) );
			add_action( 'wp_ajax_nopriv_update_item_quantity', array( 'YITH_WCWL_Ajax_Handler_Extended', 'update_quantity' ) );
		}

		/**
		 * Update quantity of an item in wishlist
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function update_quantity() {
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'update_item_quantity' ) ) {
				die();
			}

			$wishlist_token = isset( $_POST['wishlist_token'] ) ? sanitize_text_field( wp_unslash( $_POST['wishlist_token'] ) ) : false;
			$product_id     = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : false;
			$quantity       = isset( $_POST['quantity'] ) ? intval( $_POST['quantity'] ) : 1;

			if ( ! $wishlist_token || ! $product_id ) {
				die();
			}

			$wishlist = yith_wcwl_get_wishlist( $wishlist_token );

			if ( ! $wishlist || ! $wishlist->current_user_can( 'update_quantity' ) ) {
				die();
			}

			$item = $wishlist->get_product( $product_id );

			if ( ! $item ) {
				die();
			}

			$item->set_quantity( $quantity );
			$item->save();

			// stops ajax call from further execution (no return value expected on answer body).
			die();
		}
	}
}

YITH_WCWL_Ajax_Handler_Extended::init();
