<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

add_action( 'wp_enqueue_scripts', 'yith_wcms_enqueue_scripts_for_storefront', 15 );

if( ! function_exists( 'yith_wcms_enqueue_scripts_for_storefront' ) ){
	/**
	 * Add a css style for Storefront
	 *
	 * @since    1.3.13
	 * @return  void
	 */
	function yith_wcms_enqueue_scripts_for_storefront(){
		$css = "@media (min-width: 768px){.woocommerce-checkout.yith-wcms #order_review, .woocommerce-checkout.yith-wcms #order_review_heading {width: 100%; float: none;}}";
		wp_add_inline_style( 'storefront-style', $css );
	}
}