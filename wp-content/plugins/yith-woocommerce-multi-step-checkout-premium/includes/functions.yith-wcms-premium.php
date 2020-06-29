<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! function_exists( 'yith_wcms_checkout_timeline_default_icon' ) ) {
    /**
     * Get Default timeline icon options
     *
     * @param string|$step The timeline step
     *
     * @since    1.0
     * @return mixed|array|string
     */
    function yith_wcms_checkout_timeline_default_icon( $step = 'all', $size = 'original' ) {
        $default_icon = array(
            'login'         => YITH_WCMS_ASSETS_URL . 'images/icon/login.png',
            'billing'       => YITH_WCMS_ASSETS_URL . 'images/icon/billing.png',
            'shipping'      => YITH_WCMS_ASSETS_URL . 'images/icon/shipping.png',
            'order'         => YITH_WCMS_ASSETS_URL . 'images/icon/order.png',
            'payment'       => YITH_WCMS_ASSETS_URL . 'images/icon/payment.png',
        );

        if( 'original' == $size ) {
            return 'all' == $step ? $default_icon : $default_icon[ $step ];
        }

        else{
            $image_size = YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_' . $size];
            return sprintf( '%s_%dx%d.png', strstr( $default_icon[ $step ], '.png', true ), $image_size['width'], $image_size['height'] );
        }
    }
}

if ( ! function_exists( 'yith_wcms_checkout_timeline_get_icon' ) ) {
    /**
     * Get Default timeline icon options
     *
     * @param        $style The timeline style
     * @param string|$step The timeline step
     *
     * @since    1.0
     * @return mixed|array|string
     */
    function yith_wcms_checkout_timeline_get_icon( $style, $step ) {
        $image_id  = get_option( 'yith_wcms_timeline_options_icon_' . $step );

        if ( is_numeric( $image_id ) ) {
            $image_src = wp_get_attachment_image_src( $image_id, 'yith_wcms_timeline_' . $style );
            return $image_src[0];
        }

        else if( ! empty( $image_id ) ){
	        return $image_id;
        }

        else {
            return yith_wcms_checkout_timeline_default_icon( $step, $style );
        }
    }
}

if( ! function_exists( 'yith_wcms_my_account_login_form' ) ){

	/**
	 * Show My Account login form
	 *
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 * @since  1.6.1
	 *
	 * @return void
	 */
	function yith_wcms_my_account_login_form(){
		if( ! is_user_logged_in() ){
			echo do_shortcode( '[woocommerce_my_account]' );
		}
	}
}