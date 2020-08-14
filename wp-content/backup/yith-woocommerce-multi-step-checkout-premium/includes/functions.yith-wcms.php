<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! function_exists( 'yith_wcms_get_template' ) ) {
    /**
     * Get Plugin Template
     *
     * It's possible to overwrite the template from theme.
     * Put your custom template in woocommerce/product-vendors folder
     *
     * @param        $filename
     * @param array  $args
     * @param string $section
     * @use  wc_get_template()
     * @since 1.0
     * @return void
     */
    function yith_wcms_get_template( $filename, $args = array(), $section = '' ) {

        $ext = strpos( $filename, '.php' ) === false ? '.php' : '';
        $template_name      = $section . '/' . $filename . $ext;
        $template_path      = WC()->template_path() . 'multi-step/';
        $default_path       = YITH_WCMS_TEMPLATE_PATH;

        if( defined( 'YITH_WCMS_PREMIUM' ) ){
            $premium_template   = str_replace( '.php', '-premium.php', $template_name );
            $located_premium    = wc_locate_template( $premium_template, $template_path, $default_path );
            $template_name      = file_exists( $located_premium ) ?  $premium_template : $template_name;
        }

        wc_get_template( $template_name, $args, $template_path, $default_path );
    }
}

if ( ! function_exists( 'yith_wcms_checkout_timeline' ) ) {
    /**
     * Get Timeline Template
     *
     * It's possible to overwrite the template from theme.
     * Put your custom template in woocommerce/product-vendors folder
     *
     * @param array  $args      The template args
     *
     * @use  wc_get_template()
     * @since    1.0
     * @return void
     */
    function yith_wcms_load_checkout_timeline( $args ) {
        $filename           = apply_filters( 'yith_wcms_timeline_template', yith_wcms_timeline_template( $args['style'] ) );
        $section            = 'woocommerce/checkout';
        $template_name      = $section . '/' . $filename;
        $template_path      = WC()->template_path() . 'multi-step/';
        $default_path       = YITH_WCMS_TEMPLATE_PATH;

        wc_get_template( $template_name, $args, $template_path, $default_path );

        do_action( 'yith_woocommerce_show_wc_notices' );
    }
}

if( ! function_exists( 'yith_get_checkout_url' ) ){

    /**
     * Gets the url to the checkout page.
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     * @since  1.3.8
     *
     * @return string Url to checkout page
     */
    function yith_get_checkout_url(){
        return version_compare( '2.5', WC()->version, '<=' ) ? wc_get_checkout_url() : WC()->cart->get_checkout_url();
    }
}

if( ! function_exists( 'yith_wcms_login_form' ) ){

	/**
	 * Show WooCommerce login form
	 *
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 * @since  1.6.1
	 *
	 * @return void
	 */
	function yith_wcms_login_form( $login_message ){
		woocommerce_login_form(
			array(
				'message'  => $login_message,
				'redirect' => wc_get_page_permalink( 'checkout' ),
				'hidden'   => false
			)
		);
	}
}

if( ! function_exists( 'yith_wcms_step_use_icon' ) ){
	/**
	 * Check if current step use icon or not
	 *
	 * @param $step string current step
	 *
	 * @return bool|string false if the current step don't use icon or the icon type if the current step use it
	 *
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 * @since 2.0.0
	 */
	function yith_wcms_step_use_icon( $step ){
		$use_icon = get_option( 'yith_wcms_use_icon_' . $step, 'default-icon' );
		$use_icon = 'no-icon' === $use_icon ? false : $use_icon;
		return $use_icon;
	}
}

if ( ! function_exists( 'yith_wcms_timeline_template' ) ) {
	/**
	 * Check if current step use icon or not
	 *
	 * @param $style string the current checkout style
	 *
	 * @return string timeline template file to load
	 *
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 * @since 2.0.0
	 */
	function yith_wcms_timeline_template( $style ) {
		$custom_template = apply_filters( 'yith_wcms_use_custom_timeline_template', array(), $style );
		$template        = in_array( $style, $custom_template ) ? "checkout-timeline-{$style}.php" : "checkout-timeline.php";

		return $template;
	}
}
