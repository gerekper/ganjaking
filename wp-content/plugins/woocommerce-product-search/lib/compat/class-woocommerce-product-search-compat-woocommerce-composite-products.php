<?php
/**
 * class-woocommerce-product-search-compat-woocommerce-composite-products.php
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
 * @since 4.2.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Composite Products compatibility.
 */
class WooCommerce_Product_Search_Compat_WooCommerce_Composite_Products {

	/**
	 * Register the compatibility filter.
	 */
	public static function init() {

		add_filter( 'woocommerce_product_search_use_engine', array( __CLASS__, 'woocommerce_product_search_use_engine' ) );
	}

	/**
	 * Filter hook.
	 *
	 * @param boolean $use_engine
	 *
	 * @return boolean
	 */
	public static function woocommerce_product_search_use_engine( $use_engine ) {
		if ( $use_engine ) {

			$action = null;
			if ( !empty( $_GET['wc-ajax'] ) ) {
				$action = $_GET['wc-ajax'];
			}
			if ( $action === 'woocommerce_show_component_options' ) {
				$use_engine = false;
			}
		}
		return $use_engine;
	}
}
WooCommerce_Product_Search_Compat_WooCommerce_Composite_Products::init();
