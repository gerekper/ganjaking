<?php
/**
 * Smart Coupons Initialize
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     1.0
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

					$wpdb = clone $wpdb_obj; // phpcs:ignore
				}
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

			if ( false === $all_cache_key ) {
				wp_cache_flush();
				update_option( 'wc_sc_all_cache_key', array(), 'no' );
			} elseif ( ! empty( $all_cache_key ) ) {
				$cleared_cache = array();
				foreach ( $all_cache_key as $key ) {
					$is_cleared = wp_cache_delete( $key, 'woocommerce_smart_coupons' );
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
