<?php
/**
 * Smart Coupons Initialize
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     1.1.0
 * @package     WooCommerce Smart Coupons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Act_Deact' ) ) {

	/**
	 * Class for handling actions to be performed during initialization
	 */
	class WC_SC_Act_Deact {

		/**
		 * Database changes required for Smart Coupons
		 *
		 * Add option 'smart_coupon_email_subject' if not exists
		 * Enable 'Auto Generation' for Store Credit (discount_type: 'smart_coupon') not having any customer_email
		 * Disable 'apply_before_tax' for all Store Credit (discount_type: 'smart_coupon')
		 */
		public static function smart_coupon_activate() {

			set_transient( '_smart_coupons_process_activation', 1, 30 );

			if ( ! is_network_admin() && ! isset( $_GET['activate-multi'] ) ) { // phpcs:ignore
				set_transient( '_smart_coupons_activation_redirect', 1, 30 );
			}

		}

		/**
		 * Process activation
		 */
		public static function process_activation() {

			global $wpdb, $blog_id;

			$is_migrate_site_options = get_site_option( 'wc_sc_migrate_site_options', 'yes' );

			if ( 'yes' === $is_migrate_site_options ) {
				// phpcs:disable
				$default_options  = array(
					'all_tasks_count_woo_sc',                                   // => '',.
					'bulk_coupon_action_woo_sc',                                // => '',.
					'current_time_woo_sc',                                      // => '',.
					'remaining_tasks_count_woo_sc',                             // => '',.
					'smart_coupons_combine_emails',                             // => 'no',.
					'smart_coupons_is_send_email',                              // => 'yes',.
					'start_time_woo_sc',                                        // => '',.
					'wc_sc_auto_apply_coupon_ids',                              // => maybe_serialize( array() ),.
					'wc_sc_is_show_terms_notice',                               // => 'yes',.
					'wc_sc_terms_page_id',                                      // => '',.
					'woocommerce_wc_sc_combined_email_coupon_settings',         // => maybe_serialize( array() ),.
					'woocommerce_wc_sc_email_coupon_settings',                  // => maybe_serialize( array() ),.
					'woo_sc_action_data',                                       // => '',.
					'woo_sc_generate_coupon_posted_data',                       // => '',.
				);
				// phpcs:enable
				$existing_options = array();
				foreach ( $default_options as $option_name ) {
					$site_option = get_site_option( $option_name );
					if ( ! empty( $site_option ) ) {
						$existing_options[ $option_name ] = $site_option;
					}
				}
			}

			if ( is_multisite() ) {
				$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}", 0 ); // WPCS: cache ok, db call ok.
			} else {
				$blog_ids = array( $blog_id );
			}

			if ( ! get_option( 'smart_coupon_email_subject' ) ) {
				add_option( 'smart_coupon_email_subject' );
			}

			foreach ( $blog_ids as $blogid ) {

				if ( ( file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) ) && ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) ) {

					$wpdb_obj     = clone $wpdb;
					$wpdb->blogid = $blogid;
					$wpdb->set_prefix( $wpdb->base_prefix );

					$results = $wpdb->get_col(
						$wpdb->prepare(
							"SELECT postmeta.post_id FROM {$wpdb->prefix}postmeta as postmeta WHERE postmeta.meta_key = %s AND postmeta.meta_value = %s AND postmeta.post_id IN
											(SELECT p.post_id FROM {$wpdb->prefix}postmeta AS p WHERE p.meta_key = %s AND p.meta_value = %s) ",
							'discount_type',
							'smart_coupon',
							'customer_email',
							'a:0:{}'
						)
					); // WPCS: cache ok, db call ok.

					foreach ( $results as $result ) {
						update_post_meta( $result, 'auto_generate_coupon', 'yes' );
					}

					// To disable apply_before_tax option for Gift Certificates / Store Credit.
					$tax_post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = %s AND meta_value = %s", 'discount_type', 'smart_coupon' ) ); // WPCS: cache ok, db call ok.

					foreach ( $tax_post_ids as $tax_post_id ) {
						update_post_meta( $tax_post_id, 'apply_before_tax', 'no' );
					}

					if ( 'yes' === $is_migrate_site_options ) {
						$results = $wpdb->get_results( // phpcs:ignore
							$wpdb->prepare(
								"SELECT option_id,
										option_name,
										option_value
									FROM {$wpdb->prefix}options
									WHERE option_name IN (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
								'smart_coupons_is_send_email',
								'smart_coupons_combine_emails',
								'woocommerce_wc_sc_combined_email_coupon_settings',
								'woocommerce_wc_sc_email_coupon_settings',
								'wc_sc_is_show_terms_notice',
								'wc_sc_terms_page_id',
								'woo_sc_generate_coupon_posted_data',
								'start_time_woo_sc',
								'current_time_woo_sc',
								'all_tasks_count_woo_sc',
								'remaining_tasks_count_woo_sc',
								'bulk_coupon_action_woo_sc',
								'woo_sc_action_data',
								'wc_sc_auto_apply_coupon_ids'
							),
							ARRAY_A
						);
						if ( ! empty( $results ) ) {
							foreach ( $results as $result ) {
								$option_value = ( ! empty( $existing_options[ $result['option_name'] ] ) ) ? $existing_options[ $result['option_name'] ] : '';
								if ( ! empty( $option_value ) ) {
									if ( is_array( $option_value ) ) {
										$option_value = maybe_serialize( $option_value );
									}
									$wpdb->query( // phpcs:ignore
										$wpdb->prepare(
											"INSERT INTO {$wpdb->prefix}options (option_id, option_name, option_value, autoload)
												VALUES (%d,%s,%s,%s)
												ON DUPLICATE KEY UPDATE option_value = %s",
											$result['option_id'],
											$result['option_name'],
											$option_value,
											'no',
											$option_value
										)
									);
								}
							}
						}
					}

					$wpdb = clone $wpdb_obj; // phpcs:ignore
				}
			}

			if ( 'yes' === $is_migrate_site_options ) {
				update_site_option( 'wc_sc_migrate_site_options', 'no' );
			}

		}

		/**
		 * Database changes required for Smart Coupons
		 *
		 * Delete option 'sc_display_global_coupons' if exists
		 */
		public static function smart_coupon_deactivate() {
			if ( get_option( 'sc_display_global_coupons' ) !== false ) {
				delete_option( 'sc_display_global_coupons' );
			}
			if ( false === ( get_option( 'sc_flushed_rules' ) ) || 'found' === ( get_option( 'sc_flushed_rules' ) ) ) {
				delete_option( 'sc_flushed_rules' );
			}
			self::clear_cache();
		}

		/**
		 * Clear cache
		 *
		 * @return string $message
		 */
		public static function clear_cache() {

			$all_cache_key = get_option( 'wc_sc_all_cache_key' );

			if ( ! empty( $all_cache_key ) ) {
				$cleared_cache = array();
				foreach ( $all_cache_key as $key ) {
					$is_cleared = wp_cache_delete( $key );
					if ( true === $is_cleared ) {
						$cleared_cache[] = $key;
					}
				}
				// phpcs:disable
				// if ( count( $all_cache_key ) === count( $cleared_cache ) ) {
				// 	update_option( 'wc_sc_all_cache_key', array(), 'no' );
				// 	/* translators: Number of all cache key */
				// 	$message = sprintf( __( 'Successfully cleared %d cache!', 'woocommerce-smart-coupons' ), count( $all_cache_key ) );
				// } else {
				// 	$remaining = array_diff( $all_cache_key, $cleared_cache );
				// 	update_option( 'wc_sc_all_cache_key', $remaining, 'no' );
				// 	/* translators: 1. Number of cache not deleted 2. Number of all cache key */
				// 	$message = sprintf( __( 'Failed! Could not clear %1$d out of %2$d cache', 'woocommerce-smart-coupons' ), count( $remaining ), count( $all_cache_key ) );
				// }
				// phpcs:enable

				// There's no method in WordPress to detect if the cache is added successfully.
				// Therefore it's not possible to know whether it is deleted or not.
				// Hence returning a success message always.
				// Enable above block of code when we can detect saving & deleting of cache.
			}

			return __( 'Successfully cleared WooCommerce Smart Coupons cache!', 'woocommerce-smart-coupons' );
		}

	}

}
