<?php
/**
 * WooCommerce Measurement Price Calculator
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Measurement Price Calculator to newer
 * versions in the future. If you wish to customize WooCommerce Measurement Price Calculator for your
 * needs please refer to http://docs.woocommerce.com/document/measurement-price-calculator/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Measurement Price Calculator Compatibility Class
 *
 * @since 3.7.0
 */
class WC_Price_Calculator_Compatibility {


	/**
	 * Construct and initialize the class
	 *
	 * @since 3.7.0
	 */
	public function __construct() {

		// Catalog Visibility Options compatibility
		if ( wc_measurement_price_calculator()->is_plugin_active( 'woocommerce-catalog-visibility-options.php' ) ) {

			// add the pricing calculator and quantity input to products restricted by Catalog Visibility options
			add_action( 'catalog_visibility_after_alternate_add_to_cart_button', array( $this, 'catalog_visibility_options_pricing_calculator_quantity_input' ), 10 );
		}

		// Google Product Feed compatibility
		if ( wc_measurement_price_calculator()->is_plugin_active( 'woocommerce-gpf.php' ) ) {
			add_filter( 'woocommerce_gpf_feed_item', array( $this, 'google_product_feed_pricing_rules_price_adjustment' ) );
		}

		// WooCommerce Quick View compatibility
		if ( wc_measurement_price_calculator()->is_plugin_active( 'woocommerce-quick-view.php' ) ) {
			add_filter( 'wc_measurement_price_calculator_product_loop_url', array( $this, 'quick_view_product_url' ), 10, 2 );
		}

		if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.6' ) && Framework\SV_WC_Plugin_Compatibility::is_wc_version_lt( '3.8' ) ) {
			add_filter( 'woocommerce_order_item_get__reduced_stock', [ $this, 'skip_automatic_stock_adjustment' ], 10, 2 );
		} else {
			add_filter( 'woocommerce_prevent_adjust_line_item_product_stock', [ $this, 'prevent_auto_stock_adjustment' ], 10, 2 ); // WC 3.8+ only
		}
	}


	/**
	 * Add the pricing calculator and quantity input if the user can view the price
	 *
	 * @since 3.7.0
	 */
	public function catalog_visibility_options_pricing_calculator_quantity_input() {
		global $product;

		// bail if the calculator is not enabled for this product
		if ( ! $product || ! \WC_Price_Calculator_Product::calculator_enabled( $product ) ) {
			return;
		}

		// bail if current user can't view the price
		if ( class_exists( 'WC_Catalog_Restrictions_Filters' ) && ! \WC_Catalog_Restrictions_Filters::instance()->user_can_view_price( $product ) ) {
			return;
		}

		// render pricing calculator
		wc_measurement_price_calculator()->get_product_page_instance()->render_price_calculator();

		// render quantity input
		if ( ! $product->is_sold_individually() ) {

			woocommerce_quantity_input( array(
				'min_value' => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
				'max_value' => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product )
			) );
		}
	}


	/**
	 * Ensure Google Product Feed includes products with the pricing rules enabled.
	 *
	 * @since 3.8.0
	 * @param StdClass $feed_item
	 * @return StdClass
	 */
	public function google_product_feed_pricing_rules_price_adjustment( $feed_item ) {

		$product       = wc_get_product( $feed_item->ID );
		$price         = '';
		$regular_price = '';
		$sale_price    = '';

		/** @type \WC_Product|\WC_Product_Variable $product */
		if ( $product ) {

			$settings = new \WC_Price_Calculator_Settings( $product );

			// user-defined calculator with pricing rules enabled (nothing needs to be changed for user-defined calculators with no pricing rules)
			if ( $settings->pricing_rules_enabled() ) {

				$price         = $settings->get_pricing_rules_maximum_price();
				$regular_price = $settings->get_pricing_rules_maximum_regular_price();
				$sale_price    = $settings->pricing_rules_is_on_sale() ? $settings->get_pricing_rules_maximum_sale_price() : '';

			// quantity calculator with per unit pricing
			} elseif ( $settings->is_quantity_calculator_enabled() && \WC_Price_Calculator_Product::pricing_per_unit_enabled( $product ) ) {

				$measurement = null;

				// for variable products we must synchronize price levels to our per unit price
				if ( $product->is_type( 'variable' ) ) {

					// synchronize to the price per unit pricing
					\WC_Price_Calculator_Product::variable_product_sync( $product, $settings );

					// save the original price and remove the filter that we're currently within, to avoid an infinite loop
					$price         = $product->get_variation_price( 'min' );
					$regular_price = $product->get_variation_regular_price( 'min' );
					$sale_price    = $product->get_variation_sale_price( 'min' );

					// restore the original values
					\WC_Price_Calculator_Product::variable_product_unsync( $product );

				// all other product types
				} elseif ( $measurement = \WC_Price_Calculator_Product::get_product_measurement( $product, $settings ) ) {

					$measurement->set_unit( $settings->get_pricing_unit() );
					$measurement_value = $measurement ? $measurement->get_value() : null;

					if ( $measurement && $measurement_value ) {

						// convert to price per unit
						$price         = $product->get_price( 'edit' )         / $measurement_value;
						$regular_price = $product->get_regular_price( 'edit' ) / $measurement_value;
						$sale_price    = $product->get_sale_price( 'edit' )    / $measurement_value;
					}
				}
			}

			// set the feed item prices if available
			if ( ! empty( $price ) ) {

				$feed_item->price_inc_tax = wc_get_price_excluding_tax( $product, [
					'qty'   => 1,
					'price' => $price,
				] );

				$feed_item->price_ex_tax = wc_get_price_including_tax( $product, [
					'qty'   => 1,
					'price' => $price,
				] );
			}

			if ( ! empty( $regular_price ) ) {

				$feed_item->regular_price_ex_tax = wc_get_price_excluding_tax( $product, [
					'qty'   => 1,
					'price' => $regular_price,
				] );

				$feed_item->regular_price_inc_tax = wc_get_price_including_tax( $product, [
					'qty'   => 1,
					'price' => $regular_price,
				] );
			}

			if ( ! empty( $sale_price ) ) {

				$feed_item->sale_price_ex_tax = wc_get_price_excluding_tax( $product, [
					'qty'   => 1,
					'price' => $sale_price,
				] );

				$feed_item->sale_price_inc_tax = wc_get_price_including_tax( $product, [
					'qty'   => 1,
					'price' => $sale_price,
				] );
			}
		}

		return $feed_item;
	}


	/**
	 * Use add_to_cart_url() for products that have pricing calculator enabled and the non-ajax option is selected for QuickView.
	 *
	 * @internal
	 *
	 * @since 3.13.6
	 *
	 * @param string $url product URL
	 * @param \WC_Product $product product object
	 * @return string product URL
	 */
	public function quick_view_product_url( $url, $product ) {

		if ( 'non_ajax' === get_option( 'quick_view_trigger' ) ) {
			$url = $product->add_to_cart_url();
		}

		return $url;
	}


	/**
	 * Disables automatic stock adjustment in function \wc_maybe_adjust_line_item_product_stock which is added in WC 3.6
	 *
	 * @internal
	 *
	 * @since 3.16.1
	 *
	 * @param int|float $reduced_stock
	 * @param WC_Order_Item_Product $order_product
	 *
	 * @return int|float|bool
	 */
	public function skip_automatic_stock_adjustment( $reduced_stock, $order_product ) {

		if ( did_action( 'woocommerce_before_save_order_items' ) && \WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $order_product->get_product() ) ) {
			return false;
		}

		return $reduced_stock;
	}


	/**
	 * Prevents automatic stock adjustment on order item save for inventory-calculated items.
	 *
	 * @internal
	 *
	 * @since 3.16.3-dev.1
	 *
	 * @param bool $prevent_adjustment whether automatic adjustment should be prevented
	 * @param \WC_Order_Item $item order item for adjusting
	 * @return bool
	 */
	public function prevent_auto_stock_adjustment( $prevent_adjustment, $item ) {

		if ( $item instanceof \WC_Order_Item_Product && \WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $item->get_product() ) ) {
			$prevent_adjustment = true;
		}

		return $prevent_adjustment;
	}


}
