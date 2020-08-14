<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */


/* === Astra Theme Support === */
if( class_exists( 'Astra_Woocommerce' ) ){
	$Astra_Woocommerce = Astra_Woocommerce::get_instance();
	remove_action( 'wp', array( $Astra_Woocommerce, 'woocommerce_checkout' ) );
	add_action( 'wp_enqueue_scripts', 'yith_wcms_enqueue_scripts', 20 );
}

if( ! function_exists( 'yith_wcms_enqueue_scripts' ) ){
	function yith_wcms_enqueue_scripts(){
		$style = ".woocommerce.woocommerce-checkout form #order_review, .woocommerce-page.woocommerce-checkout form #order_review, .woocommerce-page.woocommerce-checkout form #order_review_heading{width: 100%; float:none; border-width: 0; padding-left: 0; padding-right: 0;}";
		wp_add_inline_style( 'woocommerce-general', $style );
	}
}