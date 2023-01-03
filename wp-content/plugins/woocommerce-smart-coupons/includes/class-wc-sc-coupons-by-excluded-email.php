<?php
/**
 * Class to handle feature Coupons By Excluded Email
 *
 * @author      StoreApps
 * @category    Admin
 * @package     wocommerce-smart-coupons/includes
 * @since       6.7.0
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Coupons_By_Excluded_Email' ) ) {

	/**
	 * Class WC_SC_Coupons_By_Excluded_Email
	 */
	class WC_SC_Coupons_By_Excluded_Email {

		/**
		 * Variable to hold instance of this class
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		private function __construct() {

			add_action( 'wc_sc_start_coupon_options_email_restriction', array( $this, 'usage_restriction' ) );
			add_action( 'save_post', array( $this, 'process_meta' ), 10, 2 );
			add_action( 'woocommerce_after_checkout_validation', array( $this, 'check_customer_coupons' ), 99, 2 );
			add_filter( 'wc_smart_coupons_export_headers', array( $this, 'export_headers' ) );
			add_filter( 'smart_coupons_parser_postmeta_defaults', array( $this, 'postmeta_defaults' ) );
			add_filter( 'is_protected_meta', array( $this, 'make_action_meta_protected' ), 10, 3 );
			add_filter( 'sc_generate_coupon_meta', array( $this, 'generate_coupon_meta' ), 10, 2 );
			add_filter( 'wc_sc_process_coupon_meta_value_for_import', array( $this, 'import_coupon_meta' ), 10, 2 );
			add_action( 'wc_sc_new_coupon_generated', array( $this, 'copy_coupon_meta' ) );
		}

		/**
		 * Get single instance of this class
		 *
		 * @return this class Singleton object of this class
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name The function name.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return result of function call
		 */
		public function __call( $function_name = '', $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Display field for coupon by excluded email
		 *
		 * @param array $args Arguments.
		 */
		public function usage_restriction( $args = array() ) {

			$coupon_id = ( ! empty( $args['coupon_id'] ) ) ? absint( $args['coupon_id'] ) : 0;
			$coupon    = ( ! empty( $args['coupon_obj'] ) ) ? $args['coupon_obj'] : null;

			$excluded_emails = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_meta' ) ) ) ? $coupon->get_meta( 'wc_sc_excluded_customer_email' ) : get_post_meta( $coupon_id, 'wc_sc_excluded_customer_email', true );

			if ( ! is_array( $excluded_emails ) || empty( $excluded_emails ) ) {
				$excluded_emails = array();
			}

			woocommerce_wp_text_input(
				array(
					'id'                => 'wc_sc_excluded_customer_email',
					'label'             => __( 'Excluded emails', 'woocommerce-smart-coupons' ),
					'placeholder'       => __( 'No restrictions', 'woocommerce-smart-coupons' ),
					'description'       => __( 'List of excluded billing emails to check against when an order is placed. Separate email addresses with commas. You can also use an asterisk (*) to match parts of an email. For example "*@gmail.com" would match all gmail addresses.', 'woocommerce-smart-coupons' ),
					'value'             => implode( ', ', $excluded_emails ),
					'desc_tip'          => true,
					'type'              => 'email',
					'class'             => '',
					'custom_attributes' => array(
						'multiple' => 'multiple',
					),
				)
			);

		}

		/**
		 * Save coupon by excluded email data in meta
		 *
		 * @param  Integer $post_id The coupon post ID.
		 * @param  WP_Post $post    The coupon post.
		 */
		public function process_meta( $post_id = 0, $post = null ) {
			if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) {
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			if ( is_int( wp_is_post_revision( $post ) ) ) {
				return;
			}
			if ( is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}
            if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( wc_clean( wp_unslash( $_POST['woocommerce_meta_nonce'] ) ), 'woocommerce_save_data' ) ) { // phpcs:ignore
				return;
			}
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
			if ( 'shop_coupon' !== $post->post_type ) {
				return;
			}

			$coupon = new WC_Coupon( $post_id );

            $excluded_emails = ( isset( $_POST['wc_sc_excluded_customer_email'] ) ) ? wc_clean( wp_unslash( $_POST['wc_sc_excluded_customer_email'] ) ) : ''; // phpcs:ignore
			$excluded_emails = explode( ',', $excluded_emails );
			$excluded_emails = array_map( 'trim', $excluded_emails );
			$excluded_emails = array_filter( $excluded_emails, 'is_email' );
			$excluded_emails = array_filter( $excluded_emails );

			if ( is_object( $coupon ) && is_callable( array( $coupon, 'update_meta_data' ) ) && is_callable( array( $coupon, 'save' ) ) ) {
				$coupon->update_meta_data( 'wc_sc_excluded_customer_email', $excluded_emails );
				$coupon->save();
			} else {
				update_post_meta( $post_id, 'wc_sc_excluded_customer_email', $excluded_emails );
			}

		}

		/**
		 * Now that we have billing email, check if the coupon is excluded to be used for the user or billing email
		 *
		 * Credit: WooCommerce
		 *
		 * @param array $posted Post data.
		 */
		public function check_customer_coupons( $posted = array() ) {
			$cart = ( function_exists( 'WC' ) && isset( WC()->cart ) ) ? WC()->cart : null;
			if ( is_a( $cart, 'WC_Cart' ) ) {
				$is_cart_empty = is_callable( array( $cart, 'is_empty' ) ) && $cart->is_empty();
				if ( false === $is_cart_empty ) {
					$applied_coupons = ( is_callable( array( $cart, 'get_applied_coupons' ) ) ) ? $cart->get_applied_coupons() : array();
					if ( ! empty( $applied_coupons ) ) {
						foreach ( $applied_coupons as $code ) {
							$coupon = new WC_Coupon( $code );

							if ( is_object( $coupon ) && is_callable( array( $coupon, 'is_valid' ) ) && $coupon->is_valid() ) {

								// Get user and posted emails to compare.
								$current_user  = wp_get_current_user();
								$billing_email = isset( $posted['billing_email'] ) ? $posted['billing_email'] : '';
								$check_emails  = array_unique(
									array_filter(
										array_map(
											'strtolower',
											array_map(
												'sanitize_email',
												array(
													$billing_email,
													$current_user->user_email,
												)
											)
										)
									)
								);

								if ( is_object( $coupon ) && is_callable( array( $coupon, 'get_meta' ) ) ) {
									$exclude_restrictions = $coupon->get_meta( 'wc_sc_excluded_customer_email' );
								} else {
									if ( is_object( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) {
										$coupon_id = $coupon->get_id();
									} else {
										$coupon_id = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
									}
									$exclude_restrictions = ( ! empty( $coupon_id ) ) ? get_post_meta( $coupon_id, 'wc_sc_excluded_customer_email', true ) : array();
								}

								if ( is_array( $exclude_restrictions ) && 0 < count( $exclude_restrictions ) && is_callable( array( $coupon, 'add_coupon_message' ) ) && is_callable( array( $cart, 'remove_coupon' ) ) && is_callable( array( $cart, 'is_coupon_emails_allowed' ) ) && $cart->is_coupon_emails_allowed( $check_emails, $exclude_restrictions ) ) {
									$coupon->add_coupon_message( WC_Coupon::E_WC_COUPON_NOT_YOURS_REMOVED );
									$cart->remove_coupon( $code );
								}

								/*
								|===========================================================================================================================================================================|
								|																																											|
								|	Before this method, WooCommerce checks for Allowed emails. 																												|
								|	And in that method, it already checks for the usage limit whether it is allowed to apply the coupon or not.																|
								|		1. If it's allowed, it means the usage limit is within reach & we can proceed with checking for excluded email.														|
								|			Because the main purpose of excluded email is to prevent application of coupon. And since the usage limit is already checked, it's not needed to check it again	|
								|		2. If it's not allowed, the process will not reach in this method, as it's already invalidated.																		|
								|																																											|
								|===========================================================================================================================================================================|
								*/
							}
						}
					}
				}
			}
		}

		/**
		 * Add meta in export headers
		 *
		 * @param  array $headers Existing headers.
		 * @return array
		 */
		public function export_headers( $headers = array() ) {

			$headers['wc_sc_excluded_customer_email'] = __( 'Excluded emails', 'woocommerce-smart-coupons' );

			return $headers;

		}

		/**
		 * Post meta defaults for excluded email ids meta
		 *
		 * @param  array $defaults Existing postmeta defaults.
		 * @return array $defaults Modified postmeta defaults
		 */
		public function postmeta_defaults( $defaults = array() ) {

			$defaults['wc_sc_excluded_customer_email'] = '';

			return $defaults;
		}

		/**
		 * Make meta data of excluded email ids protected
		 *
		 * @param bool   $protected Is protected.
		 * @param string $meta_key The meta key.
		 * @param string $meta_type The meta type.
		 * @return bool $protected
		 */
		public function make_action_meta_protected( $protected = false, $meta_key = '', $meta_type = '' ) {

			$sc_excluded_email_keys = array(
				'wc_sc_excluded_customer_email',
			);

			if ( in_array( $meta_key, $sc_excluded_email_keys, true ) ) {
				return true;
			}

			return $protected;
		}

		/**
		 * Add excluded email in coupon meta
		 *
		 * @param  array $data The row data.
		 * @param  array $post The POST values.
		 * @return array Modified data
		 */
		public function generate_coupon_meta( $data = array(), $post = array() ) {

            $excluded_emails = ( isset( $post['wc_sc_excluded_customer_email'] ) ) ? wc_clean( wp_unslash( $post['wc_sc_excluded_customer_email'] ) ) : '';  // phpcs:ignore
			$excluded_emails = explode( ',', $excluded_emails );
			$excluded_emails = array_map( 'trim', $excluded_emails );
			$excluded_emails = array_filter( $excluded_emails, 'is_email' );
			$excluded_emails = array_filter( $excluded_emails );
			$excluded_emails = implode( ',', $excluded_emails );

			$data['wc_sc_excluded_customer_email'] = $excluded_emails;

			return $data;
		}

		/**
		 * Process coupon meta value for import
		 *
		 * @param  mixed $meta_value The meta value.
		 * @param  array $args       Additional Arguments.
		 * @return mixed $meta_value
		 */
		public function import_coupon_meta( $meta_value = null, $args = array() ) {
			if ( ! empty( $args['meta_key'] ) && 'wc_sc_excluded_customer_email' === $args['meta_key'] ) {
				$excluded_emails = ( isset( $args['postmeta']['wc_sc_excluded_customer_email'] ) ) ? wc_clean( wp_unslash( $args['postmeta']['wc_sc_excluded_customer_email'] ) ) : '';  // phpcs:ignore
				$excluded_emails = explode( ',', $excluded_emails );
				$excluded_emails = array_map( 'trim', $excluded_emails );
				$excluded_emails = array_filter( $excluded_emails, 'is_email' );
				$meta_value      = array_filter( $excluded_emails );
			}
			return $meta_value;
		}

		/**
		 * Function to copy excluded email meta in newly generated coupon
		 *
		 * @param  array $args The arguments.
		 */
		public function copy_coupon_meta( $args = array() ) {

			$new_coupon_id = ( ! empty( $args['new_coupon_id'] ) ) ? absint( $args['new_coupon_id'] ) : 0;
			$coupon        = ( ! empty( $args['ref_coupon'] ) ) ? $args['ref_coupon'] : false;

			if ( empty( $new_coupon_id ) || empty( $coupon ) ) {
				return;
			}

			if ( $this->is_wc_gte_30() && is_object( $coupon ) && is_callable( array( $coupon, 'get_meta' ) ) ) {
				$excluded_emails = $coupon->get_meta( 'wc_sc_excluded_customer_email' );
			} else {
				$old_coupon_id   = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				$excluded_emails = get_post_meta( $old_coupon_id, 'wc_sc_excluded_customer_email', true );
			}

			if ( ! is_array( $excluded_emails ) || empty( $excluded_emails ) ) {
				$excluded_emails = array();
			}

			$new_coupon = new WC_Coupon( $new_coupon_id );

			if ( is_object( $new_coupon ) && is_callable( array( $new_coupon, 'update_meta_data' ) ) && is_callable( array( $new_coupon, 'save' ) ) ) {
				$new_coupon->update_meta_data( 'wc_sc_excluded_customer_email', $excluded_emails );
				$new_coupon->save();
			} else {
				update_post_meta( $new_coupon_id, 'wc_sc_excluded_customer_email', $excluded_emails );
			}

		}
	}
}

WC_SC_Coupons_By_Excluded_Email::get_instance();
