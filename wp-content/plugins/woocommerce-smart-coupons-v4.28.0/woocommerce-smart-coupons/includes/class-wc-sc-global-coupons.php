<?php
/**
 * Maintain Global Coupon's record
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     1.1.0
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
			add_action( 'save_post', array( $this, 'update_global_coupons' ), 10, 2 );
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
		 * Set global coupons in options table for faster fetching
		 */
		public function set_global_coupons() {

			global $wpdb;

			$global_coupons = get_option( 'sc_display_global_coupons' );

			$current_sc_version = get_option( 'sa_sc_db_version', '' );             // code for updating the db - for autoload related fix.

			if ( false === $global_coupons ) {
				$wpdb->query( $wpdb->prepare( 'SET SESSION group_concat_max_len=%d', 999999 ) ); // phpcs:ignore
				$wpdb->delete( $wpdb->prefix . 'options', array( 'option_name' => 'sc_display_global_coupons' ) ); // WPCS: cache ok, db call ok.
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
						'sc_display_global_coupons',
						'no',
						'shop_coupon',
						'publish',
						'sc_is_visible_storewide',
						'yes'
					)
				);

				$global_coupons = get_option( 'sc_display_global_coupons' );

				if ( ! empty( $global_coupons ) ) {
					$wpdb->query( // phpcs:ignore
						$wpdb->prepare(
							"UPDATE {$wpdb->prefix}options
								SET option_value = (SELECT IFNULL(GROUP_CONCAT(post_id SEPARATOR ','), '')
													FROM {$wpdb->prefix}postmeta
													WHERE meta_key = %s
														AND CAST(meta_value AS CHAR) = %s
														AND FIND_IN_SET(post_id, (SELECT option_value FROM (SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = %s) as temp )) > 0 )
								WHERE option_name = %s",
							'customer_email',
							'a:0:{}',
							'sc_display_global_coupons',
							'sc_display_global_coupons'
						)
					);

					$global_coupons = get_option( 'sc_display_global_coupons' );

					if ( ! empty( $global_coupons ) ) {
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
								'sc_display_global_coupons',
								'sc_display_global_coupons'
							)
						);

						$global_coupons = get_option( 'sc_display_global_coupons' );

						if ( ! empty( $global_coupons ) ) {
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
									'sc_display_global_coupons',
									'sc_display_global_coupons'
								)
							);
						}
					}

					$global_coupons = get_option( 'sc_display_global_coupons' );
				}
			}

			if ( ( empty( $current_sc_version ) || version_compare( $current_sc_version, '3.3.6', '<' ) ) && false !== $global_coupons ) {

				$wpdb->query( // phpcs:ignore
					$wpdb->prepare(
						"UPDATE {$wpdb->prefix}options
							SET autoload = %s
							WHERE option_name = %s",
						'no',
						'sc_display_global_coupons'
					)
				);

				$current_sc_version = '3.3.6';

				update_option( 'sa_sc_db_version', $current_sc_version, 'no' );
			}

		}

		/**
		 * Function to update list of global coupons
		 *
		 * @param int    $post_id The post id.
		 * @param string $action Action.
		 */
		public function sc_update_global_coupons( $post_id, $action = 'add' ) {
			if ( empty( $post_id ) ) {
				return;
			}
			if ( 'shop_coupon' !== get_post_type( $post_id ) ) {
				return;
			}

			$coupon_meta   = get_post_meta( $post_id );
			$coupon_status = get_post_status( $post_id );

			$global_coupons_list = get_option( 'sc_display_global_coupons' );
			$global_coupons      = ( ! empty( $global_coupons_list ) ) ? explode( ',', $global_coupons_list ) : array();
			$key                 = array_search( (string) $post_id, $global_coupons, true );

			if ( ( 'publish' === $coupon_status
					&& ( empty( $coupon_meta['customer_email'][0] ) || serialize( array() ) === $coupon_meta['customer_email'][0] ) // phpcs:ignore
					&& ( ! empty( $coupon_meta['sc_is_visible_storewide'][0] ) && 'yes' === $coupon_meta['sc_is_visible_storewide'][0] )
					&& ( ! empty( $coupon_meta['auto_generate_coupon'][0] ) && 'yes' !== $coupon_meta['auto_generate_coupon'][0] )
					&& ( ! empty( $coupon_meta['discount_type'][0] ) && 'smart_coupon' !== $coupon_meta['discount_type'][0] ) )
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
			if ( 'shop_coupon' !== get_post_type( $post_id ) ) {
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
			if ( 'shop_coupon' !== get_post_type( $post_id ) ) {
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
		 * @param  int    $post_id The post id.
		 * @param  object $post The post object.
		 */
		public function update_global_coupons( $post_id, $post ) {
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

			$this->sc_update_global_coupons( $post_id );

		}



	}

}

WC_SC_Global_Coupons::get_instance();
