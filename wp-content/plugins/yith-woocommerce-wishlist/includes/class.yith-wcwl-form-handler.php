<?php
/**
 * Static class that will handle all form submission from customer
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Form_Handler' ) ) {
	/**
	 * WooCommerce Wishlist Form Handler
	 *
	 * @since 3.0.0
	 */
	class YITH_WCWL_Form_Handler {
		/**
		 * Performs all required add_actions to handle forms
		 *
		 * @return void
		 */
		public static function init() {
			/**
			 * This check was added to prevent bots from accidentaly executing wishlist code
			 *
			 * @since 3.0.10
			 */
			if ( ! self::process_form_handling() ) {
				return;
			}

			// add to wishlist when js is disabled.
			add_action( 'init', array( 'YITH_WCWL_Form_Handler', 'add_to_wishlist' ) );

			// remove from wishlist when js is disabled.
			add_action( 'init', array( 'YITH_WCWL_Form_Handler', 'remove_from_wishlist' ) );

			// remove from wishlist after add to cart.
			add_action( 'woocommerce_add_to_cart', array( 'YITH_WCWL_Form_Handler', 'remove_from_wishlist_after_add_to_cart' ) );

			// change wishlist title.
			add_action( 'init', array( 'YITH_WCWL_Form_Handler', 'change_wishlist_title' ) );
		}

		/**
		 * Return true if system can process request; false otherwise
		 *
		 * @return bool
		 */
		public static function process_form_handling() {
			$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : false;

			if ( $user_agent && apply_filters( 'yith_wcwl_block_user_agent', preg_match( '/bot|crawl|slurp|spider|wordpress/i', $user_agent ), $user_agent ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Adds a product to wishlist when js is disabled
		 *
		 * @return void
		 */
		public static function add_to_wishlist() {
			// add item to wishlist when javascript is not enabled.
			if ( isset( $_GET['add_to_wishlist'] ) ) {
				try {
					YITH_WCWL()->add();

					yith_wcwl_add_notice( apply_filters( 'yith_wcwl_product_added_to_wishlist_message', get_option( 'yith_wcwl_product_added_text' ) ), 'success' );
				} catch ( Exception $e ) {
					yith_wcwl_add_notice( apply_filters( 'yith_wcwl_error_adding_to_wishlist_message', $e->getMessage() ), 'error' );
				}
			}
		}

		/**
		 * Removes from wishlist when js is disabled
		 *
		 * @return void
		 */
		public static function remove_from_wishlist() {
			// remove item from wishlist when javascript is not enabled.
			if ( isset( $_GET['remove_from_wishlist'] ) ) {
				try {
					YITH_WCWL()->remove();
				} catch ( Exception $e ) {
					yith_wcwl_add_notice( $e->getMessage(), 'error' );
				}
			}
		}

		/**
		 * Remove from wishlist after adding to cart
		 *
		 * @return void
		 */
		public static function remove_from_wishlist_after_add_to_cart() {
			if ( 'yes' != get_option( 'yith_wcwl_remove_after_add_to_cart' ) ) {
				return;
			}

			$args = array();

			if ( isset( $_REQUEST['remove_from_wishlist_after_add_to_cart'] ) ) {

				$args['remove_from_wishlist'] = intval( $_REQUEST['remove_from_wishlist_after_add_to_cart'] );

				if ( isset( $_REQUEST['wishlist_id'] ) ) {
					$args['wishlist_id'] = sanitize_text_field( wp_unslash( $_REQUEST['wishlist_id'] ) );
				}
			} elseif ( yith_wcwl_is_wishlist() && isset( $_REQUEST['add-to-cart'] ) ) {
				$args['remove_from_wishlist'] = intval( $_REQUEST['add-to-cart'] );

				if ( isset( $_REQUEST['wishlist_id'] ) ) {
					$args['wishlist_id'] = sanitize_text_field( wp_unslash( $_REQUEST['wishlist_id'] ) );
				}
			}

			if ( ! empty( $args['wishlist_id'] ) ) {
				$wishlist = yith_wcwl_get_wishlist( $args['wishlist_id'] );

				if ( $wishlist && $wishlist->is_current_user_owner() ) {
					try {
						YITH_WCWL()->remove( $args );
					} catch ( Exception $e ) {
						// we were unable to remove item from the wishlist; no follow up is provided.
					}
				}
			}
		}

		/**
		 * Change wishlist title
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public static function change_wishlist_title() {
			if ( ! isset( $_POST['yith_wcwl_edit_wishlist'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['yith_wcwl_edit_wishlist'] ) ), 'yith_wcwl_edit_wishlist_action' ) || ! isset( $_POST['save_title'] ) || empty( $_POST['wishlist_name'] ) ) {
				return;
			}

			$wishlist_name = isset( $_POST['wishlist_name'] ) ? sanitize_text_field( wp_unslash( $_POST['wishlist_name'] ) ) : false;
			$wishlist_id   = isset( $_POST['wishlist_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wishlist_id'] ) ) : false;
			$wishlist      = yith_wcwl_get_wishlist( $wishlist_id );

			if ( ! $wishlist_name || strlen( $wishlist_name ) >= 65535 ) {
				yith_wcwl_add_notice( __( 'Please, make sure to enter a valid title', 'yith-woocommerce-wishlist' ), 'error' );
			} else {
				$wishlist->set_name( $wishlist_name );
				$wishlist->save();
			}

			$redirect_url = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_REQUEST['redirect_to'] ) ) : $wishlist->get_url();

			wp_redirect( $redirect_url );
			die;
		}
	}
}

YITH_WCWL_Form_Handler::init();
