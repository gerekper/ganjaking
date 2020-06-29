<?php
/**
 * class-woocommerce-product-search-compat-storefront.php
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
 * @since 2.9.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Storefront compatibility.
 */
class WooCommerce_Product_Search_Compat_Storefront {

	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'wp_enqueue_scripts' ) );
		add_action( 'wp_footer', array( __CLASS__, 'wp_footer' ) );
	}

	public static function wp_enqueue_scripts() {
		wp_register_style( 'wps-storefront', WOO_PS_PLUGIN_URL . ( WPS_DEBUG_STYLES ? '/css/storefront.css' : '/css/storefront.min.css' ), array(), WOO_PS_PLUGIN_VERSION );
		wp_enqueue_style( 'wps-storefront' );
	}

	/**
	 * Makes the body non-scrollable while the search overlay is active.
	 * Disables the dynamicFocus especially for touch-enabled devices this will hide the results when the virtual keyboard is hidden.
	 */
	public static function wp_footer() { ?>
		<script type="text/javascript">
			document.addEventListener( "DOMContentLoaded", function() {
				if ( typeof jQuery !== "undefined" ) {
					jQuery( '.storefront-handheld-footer-bar .product-search' ).off( 'focusout' );
					jQuery( document ).on( "click touchStart", function( event ) {
						if ( jQuery( '.storefront-handheld-footer-bar .search' ).hasClass( 'active' ) ) {
							jQuery( 'body' ).addClass( 'wps-storefront-noscroll' );
						} else {
							jQuery( 'body' ).removeClass( 'wps-storefront-noscroll' );
						}
					} );
					jQuery( window ).on( "orientationchange resize", function( event ) {
						if ( !jQuery( '.storefront-handheld-footer-bar' ).is( ':visible' ) ) {
							jQuery( 'body' ).removeClass( 'wps-storefront-noscroll' );
						}
					} );
					<?php if ( is_admin_bar_showing() ) : ?>
					jQuery( '.storefront-handheld-footer-bar' ).addClass( 'admin-bar-is-showing' );
					<?php endif; ?>
				}
			} );
		</script><?php
	}
}
WooCommerce_Product_Search_Compat_Storefront::init();
