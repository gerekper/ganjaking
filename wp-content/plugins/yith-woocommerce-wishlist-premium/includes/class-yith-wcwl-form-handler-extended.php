<?php
/**
 * Static class that will handle all form submission from customer
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Form_Handler_Extended' ) ) {
	/**
	 * WooCommerce Wishlist Form Handler
	 *
	 * @since 3.0.0
	 */
	class YITH_WCWL_Form_Handler_Extended {
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
			if ( ! YITH_WCWL_Form_Handler::process_form_handling() ) {
				return;
			}

			add_action( 'init', array( 'YITH_WCWL_Form_Handler_Extended', 'update_wishlist' ) );
			add_action( 'init', array( 'YITH_WCWL_Form_Handler_Extended', 'unsubscribe' ) );
		}

		/**
		 * Update wishlist items (save quantity and position)
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public static function update_wishlist() {
			if ( ! isset( $_POST['yith_wcwl_edit_wishlist'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['yith_wcwl_edit_wishlist'] ) ), 'yith_wcwl_edit_wishlist_action' ) || ! isset( $_POST['update_wishlist'] ) || empty( $_POST['items'] ) ) {
				return;
			}

			$wishlist_id = isset( $_POST['wishlist_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wishlist_id'] ) ) : false;
			$items       = isset( $_POST['items'] ) ? array_filter( $_POST['items'] ) : false; // phpcs:ignore WordPress.Security

			if ( ! $wishlist_id || ! $items ) {
				return;
			}

			$wishlist = yith_wcwl_get_wishlist( $wishlist_id );

			if ( ! $wishlist ) {
				return;
			}

			foreach ( $items as $product_id => $values ) {
				$product_id = (int) $product_id;
				$item       = $wishlist->get_product( $product_id );

				if ( ! $item ) {
					continue;
				}

				if ( isset( $values['quantity'] ) ) {
					$item->set_quantity( (int) $values['quantity'] );
				}

				if ( isset( $values['position'] ) ) {
					$item->set_position( (int) $values['position'] );
				}

				$item->save();
			}

			wc_add_notice( __( 'Changes applied correctly', 'yith-woocommerce-wishlist' ), 'success' );

			$redirect_url = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_REQUEST['redirect_to'] ) ) : $wishlist->get_url();

			wp_safe_redirect( $redirect_url );
			die;
		}

		/**
		 * Unsubscribe from mailing lists for wishlist plugin
		 *
		 * @return void
		 */
		public static function unsubscribe() {
			// phpcs:disable WordPress.Security.NonceVerification
			if ( ! isset( $_GET['yith_wcwl_unsubscribe'] ) ) {
				return;
			}

			// retrieve unsubscription_process.
			$unsubscribe_token = isset( $_GET['yith_wcwl_unsubscribe'] ) ? sanitize_text_field( wp_unslash( $_GET['yith_wcwl_unsubscribe'] ) ) : false;
			$query_vars        = $_GET;
			// phpcs:enable WordPress.Security.NonceVerification

			if ( ! $unsubscribe_token ) {
				return;
			}

			// if user is not logged in, send to login page.
			if ( ! is_user_logged_in() ) {
				wc_add_notice( __( 'Please, log in to continue with the unsubscribe process', 'yith-woocommerce-wishlist' ), 'notice' );
				wp_safe_redirect( add_query_arg( 'redirect', esc_url( add_query_arg( $query_vars, get_home_url() ) ), wc_get_page_permalink( 'myaccount' ) ) );
				die;
			}

			// redirect uri.
			/**
			 * APPLY_FILTERS: yith_wcwl_after_unsubscribe_redirect
			 *
			 * Filter the URL to redirect after the user has unsubscribed from mailing lists.
			 *
			 * @param string $redirect_url Redirect URL
			 *
			 * @return string
			 */
			$redirect = apply_filters( 'yith_wcwl_after_unsubscribe_redirect', get_home_url() );

			// get current user token.
			$user_id                      = get_current_user_id();
			$user                         = wp_get_current_user();
			$user_unsubscribe_token       = get_user_meta( $user_id, 'yith_wcwl_unsubscribe_token', true );
			$unsubscribe_token_expiration = get_user_meta( $user_id, 'yith_wcwl_unsubscribe_token_expiration', true );

			// check for match with provided token.
			if ( $unsubscribe_token !== $user_unsubscribe_token ) {
				wc_add_notice( __( 'The token provided does not match the current user; make sure to log in with the right account', 'yith-woocommerce-wishlist' ), 'notice' );
				wp_safe_redirect( $redirect );
				die;
			}

			if ( $unsubscribe_token_expiration < time() ) {
				wc_add_notice( __( 'The token provided is expired; contact us to so we can manually unsubscribe your from the list', 'yith-woocommerce-wishlist' ), 'notice' );
				wp_safe_redirect( $redirect );
				die;
			}

			$unsubscribed_users = get_option( 'yith_wcwl_unsubscribed_users', array() );

			if ( ! in_array( $user->user_email, $unsubscribed_users, true ) ) {
				$unsubscribed_users[] = $user->user_email;

				update_option( 'yith_wcwl_unsubscribed_users', $unsubscribed_users );

				delete_user_meta( $user_id, 'yith_wcwl_unsubscribe_token' );
				delete_user_meta( $user_id, 'yith_wcwl_unsubscribe_token_expiration' );
			}

			wc_add_notice( __( 'You have unsubscribed from our wishlist-related mailing lists', 'yith-woocommerce-wishlist' ), 'success' );
			wp_safe_redirect( $redirect );
			die;
		}
	}
}

YITH_WCWL_Form_Handler_Extended::init();
