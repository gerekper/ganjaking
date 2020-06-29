<?php
/**
 * WooCommerce Cost of Goods
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Cost of Goods to newer
 * versions in the future. If you wish to customize WooCommerce Cost of Goods for your
 * needs please refer to http://docs.woocommerce.com/document/cost-of-goods/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_4_1 as Framework;

/**
 * The Cost of Goods + Measurement Price Calculator integration class.
 *
 * @since 2.7.0
 */
class WC_COG_MPC_Integration {


	/**
	 * Constructs the class.
	 *
	 * @since 2.7.0
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter( 'wc_cost_of_goods_set_order_item_cost_meta_item_cost', array( $this, 'adjust_order_line_item_cost' ), 10, 2 );
	}


	/**
	 * Enqueues the scripts.
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'wc-cog-admin-mpc-integration', wc_cog()->get_plugin_url() . '/assets/js/admin/wc-cog-admin-mpc-integration.min.js', array( 'jquery' ), \WC_COG::VERSION );
	}


	/**
	 * Adjusts the order line item costs for User-defined measurement products.
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 *
	 * @param float $item_cost order item cost
	 * @param \WC_Order_Item_Product $item order line item
	 * @return float
	 */
	public function adjust_order_line_item_cost( $item_cost, $item ) {

		if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.0' ) && $item instanceof \WC_Order_Item_Product ) {

			$product = $item->get_product();

			$measurement_data = $item->get_meta( '_measurement_data' );

		} else {

			$product_id = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
			$product    = wc_get_product( $product_id );

			$measurement_data = isset( $item['item_meta']['_measurement_data'][0] ) ? maybe_unserialize( $item['item_meta']['_measurement_data'][0] ) : null;
		}

		if ( is_array( $measurement_data ) && isset( $measurement_data['_measurement_needed_unit'] ) && $measurement_data['_measurement_needed'] && $product && \WC_Price_Calculator_Product::pricing_calculator_enabled( $product ) ) {

			$measurement_needed = new \WC_Price_Calculator_Measurement( $measurement_data['_measurement_needed_unit'], $measurement_data['_measurement_needed'] );
			$settings           = new \WC_Price_Calculator_Settings( $product );

			$item_cost *= $measurement_needed->get_value( $settings->get_pricing_unit() );
		}

		return $item_cost;
	}


}
