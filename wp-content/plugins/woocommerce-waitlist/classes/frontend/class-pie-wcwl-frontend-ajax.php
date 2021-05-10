<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Pie_WCWL_Frontend_Ajax' ) ) {
	/**
	 * Class Pie_WCWL_Frontend_Ajax
	 */
	class Pie_WCWL_Frontend_Ajax {

		/**
		 * Initialise ajax class
		 */
		public function init() {
			$this->setup_text_strings();
			$this->load_ajax();
		}

		/**
		 * Hook up ajax
		 */
		public function load_ajax() {
			// Single.
			add_action( 'wp_ajax_wcwl_process_user_waitlist_request', array( $this, 'process_user_waitlist_request' ) );
			add_action( 'wp_ajax_nopriv_wcwl_process_user_waitlist_request', array( $this, 'process_user_waitlist_request' ) );
			// Account.
			add_action( 'wp_ajax_wcwl_user_remove_self_waitlist', array( $this, 'remove_user_from_waitlist' ) );
			add_action( 'wp_ajax_wcwl_user_remove_self_archives', array( $this, 'remove_user_from_archives' ) );
		}

		/**
		 * Process the frontend user request to join/leave the given waitlist
		 * Required for simple/variable products
		 */
		public function process_user_waitlist_request() {
			$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
			$this->verify_product( $product_id );
			$lang    = isset( $_POST['language'] ) ? sanitize_text_field( $_POST['language'] ) : '';
			$email   = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
			$context = isset( $_POST['context'] ) ? sanitize_text_field( $_POST['context'] ) : '';
			if ( 'leave' === $context ) {
				$response = wcwl_remove_user_from_waitlist( $email, $product_id );
				$context  = '';
			} elseif ( 'update' === $context ) {
				$response = $this->process_grouped_product_request( $email );
			} else {
				$response = wcwl_add_user_to_waitlist( $email, $product_id, $lang );
				$context  = '';
			}
			if ( is_wp_error( $response ) ) {
				$response = $response->get_error_message();
			}
			if ( isset( $_POST['archive'] ) && 'true' === $_POST['archive'] ) {
				$html = wcwl_get_waitlist_for_archive( $product_id, $context, $response );
			} elseif ( wcwl_is_event( $product_id ) ) {
				$html = wcwl_get_waitlist_for_event( $product_id, $context, $response );
			} else {
				$html = wcwl_get_waitlist_fields( $product_id, $context, $response, $lang );
			}
			wp_send_json_success( array( 'html' => $html ) );
		}

		/**
		 * Process the frontend user request to join/leave the given waitlist/s
		 * Required for grouped products (and events)
		 */
		public function process_grouped_product_request( $email ) {
			if ( ! isset( $_POST['products'] ) || empty( $_POST['products'] ) ) {
				return new WP_Error( 'wcwl_error', __( 'No products selected', 'woocommerce-waitlist' ) );
			}
			$products = isset( $_POST['products'] ) && is_array( $_POST['products'] ) ? $_POST['products'] : array();
			$lang     = isset( $_POST['language'] ) ? sanitize_text_field( $_POST['language'] ) : '';
			foreach ( $products as $product_id => $data ) {
				if ( 'true' === $data['checked'] ) {
					wcwl_add_user_to_waitlist( $email, $product_id, $data['lang'] );
				} else {
					wcwl_remove_user_from_waitlist( $email, $product_id );
				}
			}

			return apply_filters( 'wcwl_grouped_product_joined_message_text', __( 'You have successfully updated the waitlists for the selected items', 'woocommerce-waitlist' ) );
		}

		/**
		 * Verify the given product is valid
		 */
		public function verify_product( $product_id ) {
			if ( ! wp_verify_nonce( $_POST['nonce'], 'wcwl-ajax-process-user-request-nonce' ) ) {
				wp_send_json_error( $this->nonce_not_verified_text );
			}
			$product = wc_get_product( $product_id );
			if ( $product ) {
				return $product;
			}
			if ( wcwl_is_event( $product_id ) ) {
				return tribe_events_get_event( $product_id );
			}
			wp_send_json_error( $this->invalid_product_text );
		}

		/**
		 * Process ajax request for user removing themselves from a waitlist on account pages
		 */
		public function remove_user_from_waitlist() {
			ob_start();
			$notice_type = 'success';
			$message     = '';
			if ( ! wp_verify_nonce( $_POST['wcwl_remove_user_nonce'], 'wcwl-ajax-remove-user-nonce' ) ) {
				$message     = $this->nonce_not_verified_text;
				$notice_type = 'error';
			}
			$product = isset( $_POST['product_id'] ) ? wc_get_product( absint( $_POST['product_id'] ) ) : false;
			if ( ! $product ) {
				$message     = $this->invalid_product_text;
				$notice_type = 'error';
			}
			$user = isset( $_POST['user_id'] ) ? get_user_by( 'id', absint( $_POST['user_id'] ) ) : 0;
			if ( ! $message ) {
				$message = wcwl_remove_user_from_waitlist( $user->user_email, $product->get_id() );
				if ( is_wp_error( $message ) ) {
					$message     = $message->get_error_message();
					$notice_type = 'error';
				}
			}
			wc_get_template(
				"notices/{$notice_type}.php",
				array(
					'notices'  => array( array( 'notice' => $message ) ),
					'messages' => array( $message ),
				)
			);
			$html = ob_get_clean();
			if ( 'success' == $notice_type ) {
				wp_send_json_success( $html );
			} else {
				wp_send_json_error( $html );
			}
		}

		/**
		 * Process ajax request for user removing themselves from all archives on account pages
		 */
		public function remove_user_from_archives() {
			ob_start();
			$notice_type = 'success';
			$message     = apply_filters( 'wcwl_account_removed_archives_message', __( 'You have been removed from all waitlist archives.', 'woocommerce-waitlist' ) );
			if ( ! wp_verify_nonce( $_POST['wcwl_remove_user_archive_nonce'], 'wcwl-ajax-remove-user-archive-nonce' ) ) {
				$message     = $this->nonce_not_verified_text;
				$notice_type = 'error';
			}
			$user     = isset( $_POST['user_id'] ) ? get_user_by( 'id', absint( $_POST['user_id'] ) ) : 0;
			$archives = WooCommerce_Waitlist_Plugin::get_waitlist_archives_for_user( $user );
			WooCommerce_Waitlist_Plugin::remove_user_from_archives( $archives, $user );
			wc_get_template(
				"notices/{$notice_type}.php",
				array(
					'notices'  => array( array( 'notice' => $message ) ),
					'messages' => array( $message ),
				)
			);
			$html = ob_get_clean();
			if ( 'success' == $notice_type ) {
				wp_send_json_success( $html );
			} else {
				wp_send_json_error( $html );
			}
		}

		/**
		 * Required text for ajax requests
		 */
		protected function setup_text_strings() {
			$this->nonce_not_verified_text = apply_filters( 'wcwl_error_message_invalid_nonce', __( 'There was a problem with your request: nonce could not be verified.  Please try again or contact a site administrator for help', 'woocommerce-waitlist' ) );
			$this->invalid_user_text       = apply_filters( 'wcwl_error_message_invalid_user', __( 'There was a problem with your request: the current user could not be identified.  Please try again or contact a site administrator for help', 'woocommerce-waitlist' ) );
			$this->invalid_product_text    = apply_filters( 'wcwl_error_message_invalid_product', __( 'There was a problem with your request: the selected product could not be found.  Please try again or contact a site administrator for help', 'woocommerce-waitlist' ) );
		}
	}
}
