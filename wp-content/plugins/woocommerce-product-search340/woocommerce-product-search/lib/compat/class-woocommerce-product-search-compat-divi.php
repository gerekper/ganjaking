<?php
/**
 * class-woocommerce-product-search-compat-divi.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 3.2.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !defined( 'WPS_DIVI_SHOP_FILTER' ) ) {
	define( 'WPS_DIVI_SHOP_FILTER', true );
}

/**
 * Divi compatibility.
 */
class WooCommerce_Product_Search_Compat_Divi {

	public static function init() {
		if ( apply_filters( 'woocommerce_product_search_filter_divi_shop', WPS_DIVI_SHOP_FILTER ) ) {
			add_action( 'et_pb_shop_before_print_shop', array( __CLASS__, 'et_pb_shop_before_print_shop' ) );
		}
	}

	public static function et_pb_shop_before_print_shop() {
		WooCommerce_Product_Search_Compat_Shortcodes::enable_filter_shortcode_products();

	}

}
WooCommerce_Product_Search_Compat_Divi::init();
