<?php

/**
 * Database Version Update
 */

/**
 * Remove old options for seactionstart, sectionend, title options
 */
function yith_wcms_update_db_1_0_1() {
    $db_version = get_option( 'yith_wcms_db_version', '1.0.0' );
    if ( $db_version && version_compare( $db_version, '1.0.1', '<' ) ) {
       
        $old_options = array(
            'yith_wcms_settings_options_start',
            'yith_wcms_settings_options_title',
            'yith_wcms_settings_options_end',
            'yith_wcms_order_received_options_start',
            'yith_wcms_order_received_options_title',
            'yith_wcms_order_received_options_end',
            'yith_wcms_timeline_options_title',
            'yith_wcms_timeline_options_title',
            'yith_wcms_timeline_template_options_title',
            'yith_wcms_timeline_template_options_start',
            'yith_wcms_timeline_style1_options_start',
            'yith_wcms_timeline_style2_options_start',
            'yith_wcms_timeline_style3_options_start',
            'yith_wcms_timeline_options_start',
            'yith_wcms_timeline_style_options_start',
            'yith_wcms_timeline_style_options_end',
            'yith_wcms_button_options_start',
            'yith_wcms_timeline_options_end',
            'yith_wcms_button_options_end',
            'yith_wcms_timeline_style_options_end',
            'yith_wcms_timeline_style1_options_end',
            'yith_wcms_timeline_style2_options_end',
            'yith_wcms_timeline_style3_options_end',
            'yith_wcms_settings_options_pro_title',
            'yith_wcms_settings_options_pro_start',
            'yith_wcms_settings_options_pro_end'
        );
        
        foreach( $old_options as $old_option ){
            delete_option( $old_option );
        }

        update_option( 'yith_wcms_db_version', '1.0.1' );
    }
}

/**
 * Remove old option yith_wcms_enable_multistep
 */
function yith_wcms_update_db_1_0_2() {
	$db_version = get_option( 'yith_wcms_db_version', '1.0.0' );
	if ( $db_version && version_compare( $db_version, '1.0.2', '<' ) ) {
		delete_option( 'yith_wcms_enable_multistep' );

		$default_icon = array(
			'login'         => YITH_WCMS_ASSETS_URL . 'images/icon/login.png',
			'billing'       => YITH_WCMS_ASSETS_URL . 'images/icon/billing.png',
			'shipping'      => YITH_WCMS_ASSETS_URL . 'images/icon/shipping.png',
			'order'         => YITH_WCMS_ASSETS_URL . 'images/icon/order.png',
			'payment'       => YITH_WCMS_ASSETS_URL . 'images/icon/payment.png',
		);

		foreach ( $default_icon as $step => $default ){
			$key = 'yith_wcms_timeline_options_icon_' . $step;
			$value = get_option( $key, $default );
			update_option( $key, $value );
		}

		update_option( 'yith_wcms_db_version', '1.0.2' );
	}
}

/**
 * Fix Wrong Option ID
 */
function yith_wcms_update_db_1_0_3() {
	$db_version = get_option( 'yith_wcms_db_version', '1.0.0' );
	if ( $db_version && version_compare( $db_version, '1.0.3', '<' ) ) {
		$option_value = get_option( 'yith_wcms_nav_enable_bakc_to_cart_button', 'no' );
		update_option( 'yith_wcms_nav_enable_back_to_cart_button', $option_value );
		delete_option( 'yith_wcms_nav_enable_bakc_to_cart_button' );

		update_option( 'yith_wcms_db_version', '1.0.3' );
	}
}

add_action( 'admin_init', 'yith_wcms_update_db_1_0_1' );
add_action( 'admin_init', 'yith_wcms_update_db_1_0_2' );
add_action( 'admin_init', 'yith_wcms_update_db_1_0_3' );


