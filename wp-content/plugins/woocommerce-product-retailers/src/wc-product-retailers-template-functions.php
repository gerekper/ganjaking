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
 * @package     WC-Product-Retailers/Templates
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_4 as Framework;

/**
 * Template Function Overrides.
 *
 * @since 1.0.0
 */

if ( ! function_exists( 'woocommerce_single_product_product_retailers' ) ) {

	/**
	 * Template function to include the product retailers template file.
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Product $product the product
	 * @param \WC_Product_Retailers_Retailer[] $retailers optional array of \WC_Product_Retailers_Retailer objects,
	 *                                                     otherwise any retailers associated with $product will be used
	 */
	function woocommerce_single_product_product_retailers( $product, $retailers = null ) {

		// get any retailers from the product, if not passed into the method
		if ( is_null( $retailers ) ) {
			$retailers = \WC_Product_Retailers_Product::get_product_retailers( $product );
		}

		if ( empty( $retailers ) ) {
			return;
		}

		// dropdown javascript
		if ( 'yes' === get_option( 'wc_product_retailers_enable_new_tab' ) ) {
			$javascript = '$( "select.wc-product-retailers" ).change( function() { var e = $( this ).val(); if ( e ) window.open(e); } );';
		} else {
			$javascript = '$( "select.wc-product-retailers" ).change( function() { var e = $( this ).val(); if ( e ) window.location.href = e } );';
		}

		// hide dropdown/button on variable product page until a variation is selected
		if ( $product->is_type( 'variable' ) ) {

			// show/hide the retailers button as the variation is configured/unconfigured.
			//  Also force a change event on the product variation fields so that the retailers
			//  dropdown/button is displayed on browser "back" from a retailer site
			$javascript .= '$( "form.variations_form" ).bind( "show_variation", function() { $( ".wc-product-retailers" ).slideDown( 200 ); } ); $( document ).bind( "reset_image", function( event ) { $(".wc-product-retailers").hide() } );$("form.variations_form .variations select").change();';

			// if the product is not purchasable (purchasable only from retailers) remove the 'add to cart' button
			if ( ! $product->is_purchasable() ) {
				$javascript .= '$( ".variations_button" ).remove();';
			}
		}

		// add the javascript
		wc_enqueue_js( $javascript );

		wc_get_template(
			'single-product/product-retailers.php',
			array(
				'retailers'       => $retailers,
				'open_in_new_tab' => 'yes' === get_option( 'wc_product_retailers_enable_new_tab' ),
			),
			'',
			wc_product_retailers()->get_plugin_path() . '/templates/'
		);
	}

}
