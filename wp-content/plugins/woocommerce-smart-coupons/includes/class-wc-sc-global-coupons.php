<?php
/**
 * Maintain Global Coupon's record
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     1.9.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Global_Coupons' ) ) {

	/**
	 * Class for handling global coupons
	 */
	class WC_SC_Global_Coupons {

		/**
		 * Variable to hold instance of WC_SC_Global_Coupons
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		private function __construct() {

			add_action( 'admin_init', array( $this, 'set_global_coupons' ) );
			add_action( 'woocommerce_coupon_options_save', array( $this, 'update_global_coupons' ), 99, 2 );
			add_action( 'deleted_post', array( $this, 'sc_delete_global_coupons' ) );
			add_action( 'trashed_post', array( $this, 'sc_delete_global_coupons' ) );
			add_action( 'untrashed_post', array( $this, 'sc_untrash_global_coupons' ) );
			add_action( 'future_to_publish', array( $this, 'future_to_publish_global_coupons' ) );

		}

		/**
		 * Get single instance of WC_SC_Global_Coupons
		 *
		 * @return WC_SC_Global_Coupons Singleton object of WC_SC_Global_Coupons
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
		 * Set global coupons in options table for faster fetching
		 */
		public function set_global_coupons() {
			global $wpdb;
			$global_coupon_option_name = 'sc_display_global_coupons';
			$global_coupons            = $this->sc_get_option( $global_coupon_option_name );
			$current_sc_version        = get_option( 'sa_sc_db_version', '' );             // code for updating the db - for autoload related fix.

			if ( false === $global_coupons ) {
				$wpdb->query( $wpdb->prepare( 'SET SESSION group_concat_max_len=%d', 999999 ) ); // phpcs:ignore
				$wpdb->delete( $wpdb->prefix . 'options', array( 'option_name' => $global_coupon_option_name ) ); // WPCS: cache ok, db call ok.
				$wpdb->query( // phpcs:ignore
					$wpdb->prepare(
						"REPLACE INTO {$wpdb->prefix}options (option_name, option_value, autoload)
							SELECT %s,
								IFNULL(GROUP_CONCAT(p.id SEPARATOR ','), ''),
								%s
							FROM {$wpdb->prefix}posts AS p
								JOIN {$wpdb->prefix}postmeta AS pm
										ON(pm.post_id = p.ID
											AND p.post_type = %s
											AND p.post_status = %s
											AND pm.meta_key = %s
											AND pm.meta_value = %s)
							",
						$global_coupon_option_name,
						'no',
						'shop_coupon',
						'publish',
						'sc_is_visible_storewide',
						'yes'
					)
				);

				if ( ! empty( $this->sc_get_option( $global_coupon_option_name ) ) ) {
					$wpdb->query( // phpcs:ignore
						$wpdb->prepare(
							"UPDATE {$wpdb->prefix}options
								SET option_value = (SELECT IFNULL(GROUP_CONCAT(DISTINCT pm.post_id SEPARATOR ','), '')
													FROM {$wpdb->prefix}postmeta AS pm
													WHERE ( ( pm.meta_key = %s AND CAST(pm.meta_value AS CHAR) = %s )
													  OR NOT EXISTS( SELECT 1 FROM {$wpdb->prefix}postmeta AS pm1 WHERE pm1.meta_key = %s AND pm.post_id = pm1.post_id  ) ) 
													  AND FIND_IN_SET(pm.post_id, (SELECT option_value FROM (SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = %s) as temp )) > 0 )
								WHERE option_name = %s",
							'customer_email',
							'a:0:{}',
							'customer_email',
							$global_coupon_option_name,
							$global_coupon_option_name
						)
					);

					if ( ! empty( $this->sc_get_option( $global_coupon_option_name ) ) ) {
						$wpdb->query( // phpcs:ignore
							$wpdb->prepare(
								"UPDATE {$wpdb->prefix}options
									SET option_value = (SELECT IFNULL(GROUP_CONCAT(post_id SEPARATOR ','), '')
														FROM {$wpdb->prefix}postmeta
														WHERE meta_key = %s
															AND CAST(meta_value AS CHAR) != %s
															AND FIND_IN_SET(post_id, (SELECT option_value FROM (SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = %s) as temp )) > 0 )
									WHERE option_name = %s",
								'auto_generate_coupon',
								'yes',
								$global_coupon_option_name,
								$global_coupon_option_name
							)
						);

						if ( ! empty( $this->sc_get_option( $global_coupon_option_name ) ) ) {
							$wpdb->query( // phpcs:ignore
								$wpdb->prepare(
									"UPDATE {$wpdb->prefix}options
										SET option_value = (SELECT IFNULL(GROUP_CONCAT(post_id SEPARATOR ','), '')
															FROM {$wpdb->prefix}postmeta
															WHERE meta_key = %s
																AND CAST(meta_value AS CHAR) != %s
																AND FIND_IN_SET(post_id, (SELECT option_value FROM (SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = %s) as temp )) > 0 )
										WHERE option_name = %s",
									'discount_type',
									'smart_coupon',
									$global_coupon_option_name,
									$global_coupon_option_name
								)
							);

							if ( ! empty( $this->sc_get_option( $global_coupon_option_name ) ) ) {
								$wpdb->query( // phpcs:ignore
									$wpdb->prepare(
										"UPDATE {$wpdb->prefix}options
                                    SET option_value = (SELECT IFNULL(GROUP_CONCAT(DISTINCT pm_expiry_date.post_id SEPARATOR ','), '')
                                                        FROM {$wpdb->prefix}postmeta AS pm_expiry_date 
                                                        JOIN {$wpdb->prefix}postmeta AS pm_expiry_time 
                                                        on ( pm_expiry_time.post_id = pm_expiry_date.post_id
                                                            AND pm_expiry_date.meta_key = %s
                                                            AND pm_expiry_time.meta_key = %s
                                                        )
                                                        WHERE ( (pm_expiry_date.meta_value IS NULL OR pm_expiry_date.meta_value = '') 
                                                            OR ( IFNULL(pm_expiry_date.meta_value, 0) + IFNULL(pm_expiry_time.meta_value, 0) ) > %d )
                                                            AND FIND_IN_SET(pm_expiry_date.post_id, (SELECT option_value FROM (SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = %s) as temp )) > 0 )
                                    WHERE option_name = %s",
										'date_expires',
										'wc_sc_expiry_time',
										time(),
										$global_coupon_option_name,
										$global_coupon_option_name
									)
								);
								$global_coupons = $this->sc_get_option( $global_coupon_option_name );
							}
						}
					}
				}
			}

			if ( ( empty( $current_sc_version ) || version_compare( $current_sc_version, '3.3.6', '<' ) ) && ! empty( $this->sc_get_option( $global_coupon_option_name ) ) ) {

				$wpdb->query( // phpcs:ignore
					$wpdb->prepare(
						"UPDATE {$wpdb->prefix}options
							SET autoload = %s
							WHERE option_name = %s",
						'no',
						$global_coupon_option_name
					)
				);

				$current_sc_version = '3.3.6';

				update_option( 'sa_sc_db_version', $current_sc_version, 'no' );
			}

		}

		/**
		 * Function to update list of global coupons
		 *
		 * @param int       $post_id The post id.
		 * @param string    $action Action.
		 * @param WC_Coupon $coupon The coupon object.
		 */
		public function sc_update_global_coupons( $post_id, $action = 'add', $coupon = null ) {
			if ( empty( $post_id ) ) {
				return;
			}
			if ( 'shop_coupon' !== $this->get_post_type( $post_id ) ) {
				return;
			}

			$coupon = new WC_Coupon( $post_id );

			$coupon_status = ( $this->is_wc_greater_than( '6.1.2' ) && $this->is_callable( $coupon, 'get_status' ) ) ? $coupon->get_status() : get_post_status( $post_id );

			if ( $this->is_callable( $coupon, 'get_meta' ) ) {
				$customer_email          = $coupon->get_email_restrictions();
				$sc_is_visible_storewide = $coupon->get_meta( 'sc_is_visible_storewide' );
				$auto_generate_coupon    = $coupon->get_meta( 'auto_generate_coupon' );
				$discount_type           = $coupon->get_discount_type();
			} else {
				$coupon_meta             = get_post_meta( $post_id );
				$customer_email          = ( ! empty( $coupon_meta['customer_email'] ) ) ? $coupon_meta['customer_email'][0] : '';
				$sc_is_visible_storewide = ( ! empty( $coupon_meta['sc_is_visible_storewide'] ) ) ? $coupon_meta['sc_is_visible_storewide'][0] : '';
				$auto_generate_coupon    = ( ! empty( $coupon_meta['auto_generate_coupon'] ) ) ? $coupon_meta['auto_generate_coupon'][0] : '';
				$discount_type           = ( ! empty( $coupon_meta['discount_type'] ) ) ? $coupon_meta['discount_type'][0] : '';
			}

			$global_coupons_list = get_option( 'sc_display_global_coupons' );
			$global_coupons      = ( ! empty( $global_coupons_list ) ) ? explode( ',', $global_coupons_list ) : array();
			$key                 = array_search( (string) $post_id, $global_coupons, true );

			if ( ( 'publish' === $coupon_status
					&& ( empty( $customer_email ) || serialize( array() ) === $customer_email ) // phpcs:ignore
					&& ( ! empty( $sc_is_visible_storewide ) && 'yes' === $sc_is_visible_storewide )
					&& ( ! empty( $auto_generate_coupon ) && 'yes' !== $auto_generate_coupon )
					&& ( ! empty( $discount_type ) && 'smart_coupon' !== $discount_type ) )
				|| ( 'trash' === $coupon_status && 'delete' === $action ) ) {

				if ( 'add' === $action && false === $key ) {
					$global_coupons[] = $post_id;
				} elseif ( 'delete' === $action && false !== $key ) {
					unset( $global_coupons[ $key ] );
				}
			} else {
				if ( false !== $key ) {
					unset( $global_coupons[ $key ] );
				}
			}

			update_option( 'sc_display_global_coupons', implode( ',', array_unique( $global_coupons ) ), 'no' );
		}

		/**
		 * Function to update list of global coupons on trash / delete coupon
		 *
		 * @param int $post_id The post id.
		 */
		public function sc_delete_global_coupons( $post_id ) {
			if ( empty( $post_id ) ) {
				return;
			}
			if ( 'shop_coupon' !== $this->get_post_type( $post_id ) ) {
				return;
			}

			$this->sc_update_global_coupons( $post_id, 'delete' );
		}

		/**
		 * Function to update list of global coupons on untrash coupon
		 *
		 * @param int $post_id The post id.
		 */
		public function sc_untrash_global_coupons( $post_id ) {
			if ( empty( $post_id ) ) {
				return;
			}
			if ( 'shop_coupon' !== $this->get_post_type( $post_id ) ) {
				return;
			}

			$this->sc_update_global_coupons( $post_id );
		}

		/**
		 * Update global coupons data for sheduled coupons
		 *
		 * @param  WP_Post $post The post object.
		 */
		public function future_to_publish_global_coupons( $post = null ) {
			$post_id = ( ! empty( $post->ID ) ) ? $post->ID : 0;

			if ( empty( $post ) || empty( $post_id ) ) {
				return;
			}
			if ( 'shop_coupon' !== $post->post_type ) {
				return;
			}

			$this->sc_update_global_coupons( $post_id );
		}

		/**
		 * Update global coupons on saving coupon
		 *
		 * @param  int       $post_id The post id.
		 * @param  WC_Coupon $coupon The coupon object.
		 */
		public function update_global_coupons( $post_id = 0, $coupon = null ) {
			if ( empty( $post_id ) ) {
				return;
			}

			if ( is_null( $coupon ) || ! is_a( $coupon, 'WC_Coupon' ) ) {
				$coupon = new WC_Coupon( $post_id );
			}

			$this->sc_update_global_coupons( $post_id, 'add', $coupon );

		}

	}

}

WC_SC_Global_Coupons::get_instance();
