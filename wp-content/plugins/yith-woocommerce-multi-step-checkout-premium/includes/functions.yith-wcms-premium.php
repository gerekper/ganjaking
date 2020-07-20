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
    function yith_wcms_checkout_timeline_default_icon( $step = 'all' ) {
    	$icons_path = YITH_WCMS_ASSETS_PATH . 'images/icons/';

	    if( 'all' !== $step ){
		    $icon_name = get_option( 'yith_wcms_timeline_options_default_icon_' . $step, $step );
		    $icon = $icons_path . $icon_name . '.svg';
    		return $icon;
	    }

    	else {
		    $default_steps = array(
			    'login',
			    'billing',
			    'shipping',
			    'order',
			    'payment',
			    'login2',
			    'billing2',
			    'shipping2',
			    'order2',
			    'payment2',
		    );

		    $icons = array();

		    foreach ( $default_steps as $step ){
			    $icon_name = get_option( 'yith_wcms_timeline_options_default_icon_' . $step, $step );
		    	$icons[ $step ] = $icons_path . $icon_name . '.svg';
		    }

		    return $icons;
	    }
    }
}

if ( ! function_exists( 'yith_wcms_checkout_timeline_get_icon' ) ) {
    /**
     * Get Default timeline icon options
     *
     * @param $style string the timeline style
     * @param $step string The timeline step
     *
     * @since  2.0
     * @return mixed|array|string
     */
    function yith_wcms_checkout_timeline_get_icon( $style, $step ) {
	    $labels      = apply_filters( 'yith_wcms_timeline_labels', array() );
	    $image_class = apply_filters( 'yith_wcms_timeline_icon_class', '' );
	    $use_icon    = $use_icon_for_login_step = yith_wcms_step_use_icon( $step );

    	if( 'default-icon' == $use_icon ){
    		$icon = yith_wcms_checkout_timeline_default_icon( $step );
    		ob_start();
    		require( $icon );
    		$icon_content = ob_get_clean();
		    return $icon_content;
	    }

    	else {
		    $image_id  = get_option( 'yith_wcms_timeline_options_icon_' . $step );

		    if ( is_numeric( $image_id ) ) {
			    $image_src = wp_get_attachment_image_src( $image_id, 'yith_wcms_timeline_' . $style );
			    $display   = wp_is_mobile() ? 'vertical' : get_option( 'yith_wcms_timeline_display', 'horizontal' );
			    $sizes     = ! empty( YITH_Multistep_Checkout()->sizes[ 'yith_wcms_timeline_' . $style . '_' . $display ] ) ? YITH_Multistep_Checkout()->sizes['yith_wcms_timeline_' . $style . '_' . $display] : YITH_Multistep_Checkout()->sizes[ 'yith_wcms_timeline_' . $style ];
			    $width     = $sizes['width'];
			    $height    = $sizes['height'];
			    sprintf ( '<img src="%s" alt="%s" class="%s" width="%s" height="%s" />', $image_src, $labels[ $step ], $image_class, $width, $height );
			    return $image_src[0];
		    }

		    else {
			    return $image_id;
		    }
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
