<?php
/**
 * WooCommerce Product Reviews Pro
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Product Reviews Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Product_Reviews_Pro;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Plugin lifecycle handler.
 *
 * @since 1.13.0
 *
 * @method \WC_Product_Reviews_Pro get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Lifecycle constructor.
	 *
	 * @since 1.13.0
	 *
	 * @param \WC_Product_Reviews_Pro $plugin
	 */
	public function __construct( $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = [
			'1.11.0',
			'1.12.0',
			'1.12.1',
		];
	}


	/**
	 * Flushes rewrite rules upon activation.
	 *
	 * @internal
	 *
	 * @since 1.13.0
	 */
	public function activate() {

		flush_rewrite_rules();
	}


	/**
	 * Flushes rewrite rules upon deactivation.
	 *
	 * @internal
	 *
	 * @since 1.13.0
	 */
	public function deactivate() {

		flush_rewrite_rules();
	}


	/**
	 * Handles installation routine.
	 *
	 * @internal
	 *
	 * @since 1.13.0
	 */
	protected function install() {
		global $wpdb;

		// Default settings
		update_option( 'wc_product_reviews_pro_enabled_contribution_types', 'all' );
		update_option( 'wc_product_reviews_pro_contributions_orderby',      'most_helpful' );
		update_option( 'wc_product_reviews_pro_contribution_moderation',    get_option( 'comment_moderation' ) ? 'yes' : 'no' );
		update_option( 'wc_product_reviews_pro_contribution_threshold',     get_option( 'wc_product_reviews_pro_contribution_threshold', 1 ) );
		update_option( 'wc_product_reviews_pro_contribution_badge',         get_option( 'wc_product_reviews_pro_contribution_badge', __( 'Admin', 'woocommerce-product-reviews-pro' ) ) );
		update_option( 'wc_product_reviews_pro_contribution_badge_vendor',  get_option( 'wc_product_reviews_pro_contribution_badge_vendor', __( 'Vendor', 'woocommerce-product-reviews-pro' ) ) );

		// Set comment_type to 'review' on all comments that have a product as
		// their parent and no type set.  Page through comments in blocks to
		// avoid out of memory errors
		$offset           = (int) get_option( 'wc_product_reviews_pro_install_offset', 0 );
		$records_per_page = 500;

		do {

			$record_ids = get_comments( [
				'post_type' => 'product',
				'type'      => '',
				'fields'    => 'ids',
				'offset'    => $offset,
				'number'    => $records_per_page,
			] );

			// some sort of bad database error: deactivate the plugin and display an error
			if ( is_wp_error( $record_ids ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				deactivate_plugins( 'woocommerce-product-reviews-pro/woocommerce-product-reviews-pro.php' );

				wp_die(
					sprintf( /* translators: Placeholders: %1$s - plugin name, %2$s - error message(s) */
						__( 'Error activating and installing %1$s: %2$s', 'woocommerce-product-reviews-pro' ),
						$this->get_plugin()->get_plugin_name(),
						'<ul><li>' . implode( '</li><li>', $record_ids->get_error_messages() ) . '</li></ul>' ) .
					'<a href="' . admin_url( 'plugins.php' ) . '">' . esc_html__( '&laquo; Go Back', 'woocommerce-product-reviews-pro' ) . '</a>'
				);
			}

			if ( is_array( $record_ids ) ) {
				foreach ( $record_ids as $id ) {
					$wpdb->query( "UPDATE {$wpdb->comments} SET comment_type = 'review' WHERE comment_type = '' AND comment_ID = {$id}" );
				}
			}

			// increment offset
			$offset += $records_per_page;
			// and keep track of how far we made it in case we hit a script timeout
			update_option( 'wc_product_reviews_pro_install_offset', $offset );

		// while full set of results returned (meaning there may be more results still to retrieve)
		} while( count( $record_ids ) === $records_per_page );

		flush_rewrite_rules();
	}


	/**
	 * Runs plugin upgrade routines.
	 *
	 * @internal
	 *
	 * @since 1.13.0
	 *
	 * @param string $installed_version
	 */
	protected function upgrade( $installed_version ) {

		parent::upgrade( $installed_version );

		flush_rewrite_rules();
	}


	/**
	 * Updates to v1.11.0
	 *
	 * @since 1.13.0
	 */
	protected function upgrade_to_1_11_0() {

		// update settings for installation that updated from 1.10.0
		update_option( 'wc_product_reviews_pro_contribution_threshold',     get_option( 'wc_product_reviews_pro_contribution_threshold',    1 ) );
		update_option( 'wc_product_reviews_pro_contribution_badge',         get_option( 'wc_product_reviews_pro_contribution_badge',        __( 'Admin', 'woocommerce-product-reviews-pro' ) ) );
		update_option( 'wc_product_reviews_pro_contribution_badge_vendor',  get_option( 'wc_product_reviews_pro_contribution_badge_vendor', __( 'Vendor', 'woocommerce-product-reviews-pro' ) ) );
	}


	/**
	 * Updates to v1.12.0
	 *
	 * @since 1.13.0
	 */
	protected function upgrade_to_1_12_0() {

		$threshold = get_option( 'wc_product_reviews_pro_contribution_threshold' );

		// Really ensure that the threshold is set to one in new installs or installs that haven't saved settings before.
		if ( ! is_numeric( $threshold ) ) {

			update_option( 'wc_product_reviews_pro_contribution_threshold', 1 );
		}
	}


	/**
	 * Updates to v1.12.1
	 *
	 * @since 1.13.0
	 */
	protected function upgrade_to_1_12_1() {
		global $wpdb;

		// clear our plugin's transients since we fixed a bug with them not clearing
		$wpdb->query( "
			DELETE FROM {$wpdb->options} 
			WHERE option_name LIKE '_transient_wc_product_reviews_pro_%'
		" );
	}


}
