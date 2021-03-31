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

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_6 as Framework;

/**
 * Retailers Product class
 *
 * @since 1.0.0
 */
class WC_Product_Retailers_Product {


	/**
	 * Returns true if the given product has retailers available to display.
	 *
	 * @since 1.0.0
	 *
	 * @param int|\WC_Product $product Product object or post ID
	 * @return bool
	 */
	public static function has_retailers( $product ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		$retailers = $product->get_meta( '_wc_product_retailers' );

		// NOTE: this will return true even if all wc_product_retailers have been trashed or permanently deleted
		return ! empty( $retailers );
	}


	/**
	 * Returns true if the given product is available only for purchase from a retailer.
	 *
	 * @since 1.0.0
	 *
	 * @param int|\WC_Product $product Product object or post ID
	 * @return bool
	 */
	public static function is_retailer_only_purchase( $product ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		return 'replace_store' === $product->get_meta( '_wc_product_retailers_retailer_availability' );
	}


	/**
	 * Returns true if buttons, rather than a dropdown,
	 * should be used if this product has multiple retailers.
	 *
	 * @since 1.3.0
	 * @param int|\WC_Product $product Product object or post ID
	 * @return bool
	 */
	public static function use_buttons( $product ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		return 'yes' === $product->get_meta( '_wc_product_retailers_use_buttons' );
	}


	/**
	 * Returns $price formatted with currency symbol and decimals, as configured within WooCommerce settings.
	 *
	 * Annoyingly, WC doesn't seem to offer a function to format a price string without HTML tags, so this method is adapted from the core wc_price() function.
	 *
	 * @see wc_price()
	 *
	 * @since 1.3.0
	 *
	 * @param string $price the price
	 * @return string price formatted
	 */
	public static function wc_price( $price ) {

		if ( 0 === $price ) {
			return __( 'Free!', 'woocommerce-product-retailers' );
		}

		$num_decimals    = wc_get_price_decimals();
		$currency_symbol = html_entity_decode( get_woocommerce_currency_symbol() );
		$decimal_sep     = wc_get_price_decimal_separator();
		$thousands_sep   = wc_get_price_thousand_separator();
		$price_format    = str_replace( '&nbsp;', ' ', get_woocommerce_price_format() );
		$negative        = $price < 0;
		$price           = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * -1 : $price ) );
		$price           = apply_filters( 'formatted_woocommerce_price', number_format( $price, $num_decimals, $decimal_sep, $thousands_sep ), $price, $num_decimals, $decimal_sep, $thousands_sep );

		if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $num_decimals > 0 ) {
			$price = wc_trim_zeros( $price );
		}

		return ( $negative ? '-' : '' ) . sprintf( $price_format, $currency_symbol, $price );
	}


	/**
	 * Returns the product button text for the given product.
	 *
	 * This is shown on the product page dropdown/button linking to the retailer.
	 *
	 * @since 1.0.0
	 *
	 * @param int|\WC_Product $product Product object or post ID
	 * @return string the product button text
	 */
	public static function get_product_button_text( $product ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		$button_text = $product->get_meta( '_wc_product_retailers_product_button_text' );

		if ( ! $button_text ) {
			$button_text = wc_product_retailers()->get_product_button_text();
		}

		return $button_text;
	}


	/**
	 * Returns the catalog button text for the given product.
	 *
	 * This is shown for the catalog page 'add to cart' button text if this is a simple product that is only sold through retailers.
	 *
	 * @since 1.0.0
	 *
	 * @param int|\WC_Product $product Product object or post ID
	 * @return string the catalog button text
	 */
	public static function get_catalog_button_text( $product ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		$button_text = $product->get_meta( '_wc_product_retailers_catalog_button_text' );

		if ( ! $button_text ) {
			$button_text = wc_product_retailers()->get_catalog_button_text();
		}

		return $button_text;
	}


	/**
	 * Determines whether the product retailers dropdown/buttons should be displayed by default on the product page.
	 *
	 * @since 1.3.2
	 *
	 * @param int|\WC_Product $product Product object or post ID
	 * @return bool true if the product retailers dropdown/buttons should not
	 *         not be automatically displayed on the product page, false if they
	 *         should (default is false)
	 */
	public static function product_retailers_hidden( $product ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		return 'yes' === $product->get_meta( '_wc_product_retailers_hide' );
	}


	/**
	 * Determines whether the product retailers dropdown/buttons should be hidden.
	 *
	 * This is based on the option selected and the stock status.
	 *
	 * @since 1.6.0
	 * @param int|\WC_Product $product Product object or post ID
	 * @return bool true if the product retailers dropdown/buttons should not
	 *         not be automatically displayed (set to hide if product is in stock), false if they
	 *         should (default is false)
	 */
	public static function product_retailers_hidden_if_in_stock( $product ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		return ! $product->is_type( 'variable' ) && 'out_of_stock' === $product->get_meta( '_wc_product_retailers_retailer_availability' ) && $product->is_in_stock();
	}


	/**
	 * Returns an array of retailers for the given product.
	 *
	 * These retailers are "available", meaning they are not in the trash and have a URL/name.
	 *
	 * @since 1.0.0
	 *
	 * @param int|\WC_Product $product Product object or post ID
	 * @return \WC_Product_Retailers_Retailer[]
	 */
	public static function get_product_retailers( $product ) {

		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		$product_retailers = $product->get_meta( '_wc_product_retailers' );
		$retailers         = [];

		if ( is_array( $product_retailers ) ) {

			foreach ( $product_retailers as $retailer_data ) {

				try {

					// get retailer object
					$retailer = new WC_Product_Retailers_Retailer( $retailer_data['id'] );

					// if a URL was set at the product level, use it
					if ( ! empty( $retailer_data['product_url'] ) ) {
						$retailer->set_url( $retailer_data['product_url'] );
					}

					// if a price was specified, set it (isset so 0.00 prices can be used)
					if ( isset( $retailer_data['product_price'] ) ) {
						$retailer->set_price( $retailer_data['product_price'] );
					}

					if ( $retailer->is_available() ) {
						$retailers[] = $retailer;
					}

				} catch ( \Exception $e ) { /* retailer does not exist */ }
			}
		}

		return apply_filters( 'woocommerce_get_product_retailers', $retailers, $product );
	}


}
