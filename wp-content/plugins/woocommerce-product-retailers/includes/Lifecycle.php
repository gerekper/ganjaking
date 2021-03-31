<?php
/**
 * WooCommerce Product Retailers
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Retailers to newer
 * versions in the future. If you wish to customize WooCommerce Product Retailers for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-retailers/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Product_Retailers;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_6 as Framework;

/**
 * Plugin lifecycle handler.
 *
 * @since 1.11.0
 *
 * @method \WC_Product_Retailers get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Lifecycle constructor.
	 *
	 * @since 1.11.1
	 *
	 * @param \WC_Product_Retailers $plugin
	 */
	public function __construct( $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = [
			'1.8.2',
		];
	}


	/**
	 * Runs installation scripts.
	 *
	 * @since 1.11.0
	 */
	protected function install() {

		$admin = $this->get_plugin()->get_admin_instance();

		if ( ! $admin instanceof \WC_Product_Retailers_Admin ) {
			$admin = $this->get_plugin()->load_class( '/includes/admin/class-wc-product-retailers-admin.php', 'WC_Product_Retailers_Admin' );
		}

		// install default settings
		foreach ( $admin::get_global_settings() as $setting ) {

			if ( isset( $setting['id'], $setting['default'] ) ) {

				update_option( $setting['id'], $setting['default'] );
			}
		}
	}


	/**
	 * Updates to version 1.8.2
	 *
	 * Updates product meta key for retailers only purchasing and hiding retailers if in stock.
	 *
	 * @since 1.11.1
	 */
	protected function upgrade_to_1_8_2() {
		global $wpdb;

		$hide_if_in_stock_count       = 0;
		$retailer_only_purchase_count = 0;
		$retailer_with_store_count    = 0;

		$product_ids = $wpdb->get_col( "
			SELECT ID
			FROM $wpdb->posts
			WHERE post_type IN ( 'product','product_variation' )
		" );

		foreach ( (array) $product_ids as $id ) {

			// ensure this is a real live ID
			if ( ! is_numeric( $id ) ) {
				continue;
			}

			$id = (int) $id;

			$hide_if_in_stock       = get_post_meta( $id, '_wc_product_retailers_hide_if_in_stock', true );
			$retailer_only_purchase = get_post_meta( $id, '_wc_product_retailers_retailer_only_purchase', true );

			// skip products that don't have this meta set
			if ( ! $hide_if_in_stock && ! $retailer_only_purchase ) {
				continue;
			}

			// products that hide retailers if in stock should always do so
			if ( 'yes' === $hide_if_in_stock ) {

				update_post_meta( $id, '_wc_product_retailers_retailer_availability', 'out_of_stock' );

				$hide_if_in_stock_count++;

			// products marked 'retailer only' should remain that way
			} elseif ( 'yes' === $retailer_only_purchase ) {

				update_post_meta( $id, '_wc_product_retailers_retailer_availability', 'replace_store' );

				$retailer_only_purchase_count++;

			// products that disable 'retailers only' should show retailers and the 'add to cart' button
			} elseif ( 'no' === $retailer_only_purchase ) {

				update_post_meta( $id, '_wc_product_retailers_retailer_availability', 'with_store' );

				$retailer_with_store_count++;
			}
		}

		$this->get_plugin()->log( sprintf( '%s products updated for "Hide retailers if in stock".', $hide_if_in_stock_count ) );
		$this->get_plugin()->log( sprintf( '%s products updated for "Retailer only purchase".',     $retailer_only_purchase_count ) );
		$this->get_plugin()->log( sprintf( '%s products updated for "Retailer or store purchase".', $retailer_with_store_count ) );
	}


}
