<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */


/**
 * Look the file includes/class.yith-multistep-checkout.php at line 106
 * for this add_filter:
 *
 * add_filter( 'avada_setting_get_woocommerce_one_page_checkout', '__return_zero' );
 *
 * WooCommerce One Page Checkout option must set to OFF
 */

global $avada_woocommerce;

if( ! empty( $avada_woocommerce ) ){
    /**
     * Add a filter to remove top user container box if someone don't want it
     */
    if( apply_filters( 'yith_wcms_remove_avada_top_user_container', false ) ){
        remove_action( 'woocommerce_before_checkout_form', array( $avada_woocommerce, 'avada_top_user_container' ), 1 );
    }

    /**
     * Add a filter to remove Avada coupon form if someone don't want it
     */
    if( apply_filters( 'yith_wcms_remove_avada_checkout_coupon_form', false ) ){
        remove_action( 'woocommerce_before_checkout_form', array( $avada_woocommerce, 'checkout_coupon_form' ), 10 );
    }

    remove_action( 'woocommerce_before_checkout_form', array( $avada_woocommerce, 'before_checkout_form' )  );
    remove_action( 'woocommerce_after_checkout_form',  array( $avada_woocommerce, 'after_checkout_form' ) );

    /**
     * Remove the WooCommerce coupon form, use coupon form added By avada theme
     */
    if( apply_filters( 'yith_wcms_remove_woocommerce_checkout_coupon_form', true ) ){
        remove_action( 'yith_woocommerce_checkout_coupon', 'woocommerce_checkout_coupon_form', 10 );
    }

    add_filter( 'yith_wcms_step_button_class', 'yith_wcms_step_button_class_for_avada' );
    add_action( 'wp_enqueue_scripts', 'yith_wcms_enqueue_scripts_for_avada', 15 );
}

/* === Avada Theme Support === */
if( ! function_exists( 'yith_wcms_step_button_class_for_avada' ) ){
    /**
     * Add the css class to step button for Avada Theme
     *
     * @param $classes  button fronted classes
     *
     * @since    1.3.11
     * @return  string Css classes file
     */
    function yith_wcms_step_button_class_for_avada( $classes ){
        $classes .= ' fusion-button button-default button-medium default medium';
        return $classes;
    }
}

if( ! function_exists( 'yith_wcms_enqueue_scripts_for_avada' ) ){
    /**
     * Add a css style for Avada
     *
     * @since    1.3.11
     * @return  void
     */
    function yith_wcms_enqueue_scripts_for_avada(){
        $css = '.yith-wcms ul.woocommerce-side-nav.woocommerce-checkout-nav{display: none;}';
        $css .= '.yith-wcms .woocommerce-checkout a.continue-checkout{display: none;}';
        $css .= '.yith-wcms.fusion-body {margin: 0 auto;}';
        $css .= '.yith-wcms.fusion-body form.checkout #order_review{display: block !important;}';
        wp_add_inline_style( 'avada-stylesheet', $css );
    }
}