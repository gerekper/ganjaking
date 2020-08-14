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

		foreach ( $old_options as $old_option ) {
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
			'login'    => YITH_WCMS_ASSETS_URL . 'images/icon/login.png',
			'billing'  => YITH_WCMS_ASSETS_URL . 'images/icon/billing.png',
			'shipping' => YITH_WCMS_ASSETS_URL . 'images/icon/shipping.png',
			'order'    => YITH_WCMS_ASSETS_URL . 'images/icon/order.png',
			'payment'  => YITH_WCMS_ASSETS_URL . 'images/icon/payment.png',
		);

		foreach ( $default_icon as $step => $default ) {
			$key   = 'yith_wcms_timeline_options_icon_' . $step;
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

/**
 * MultiStep 2.0
 */
function yith_wcms_update_db_2_0_0() {
	$db_version = get_option( 'yith_wcms_db_version', '1.0.0' );
	if ( $db_version && version_compare( $db_version, '2.0.0', '<' ) ) {
		/**
		 * Store old option with same ID in a temporary option
		 */
		$old_options = array(
			'yith_wcms_timeline_style1_step_background_color' => '#b2b2b0',
		);

		foreach( $old_options as $option_id => $default_value ){
			$stored_value = get_option( $option_id, $default_value );
			add_option( $option_id . '_temp', $stored_value );
			delete_option( $option_id ); //Make sure that add_option works fine!
		}

		/**
		 * Create New Options
		 * OR
		 * Restore the old option in new Panel
		 * for version 2.0.0
		 */
		$step_count_type = get_option( 'yith_wcms_timeline_step_count_type', 'number' );
		$step_separator  = get_option( 'yith_wcms_text_step_separator', '/' );

		/* Style1 */
		$style1_step_background_color          = get_option( 'yith_wcms_timeline_style1_background_color', 'e2e2e2' );
		$style1_current_step_background_color  = get_option( 'yith_wcms_timeline_style1_active_background_color', '1e8cbe' );
		$style1_step_text_color                = get_option( 'yith_wcms_timeline_style1_step_label_color', '#8b8b8b' );
		$style1_currentstep_text_color         = get_option( 'yith_wcms_timeline_style1_current_step_label_color', '#ffffff' );
		$style1_square_background_color        = get_option( 'yith_wcms_timeline_style1_step_background_color_temp', '#b2b2b0' );
		$style1_currentsquare_background_color = get_option( 'yith_wcms_timeline_style1_current_step_background_color', '#005b84' );
		$style1_square_text_color              = get_option( 'yith_wcms_timeline_style1_step_color', '#ffffff' );
		$style1_currentsquare_text_color       = get_option( 'yith_wcms_timeline_style1_current_step_color', '#ffffff' );

		/* Style2 */
		$style2_step_border_color                   = get_option( 'yith_wcms_timeline_style2_border_color', '#eeeeee' );
		$style2_step_background_color               = get_option( 'yith_wcms_timeline_style2_background_color', 'rgba(255,255,255,0)' );
		$style2_currentstep_background_color        = get_option( 'yith_wcms_timeline_style2_active_background_color', 'rgba(255,255,255,0)' );
		$style2_step_circle_background_color        = get_option( 'yith_wcms_timeline_style2_bubble_background_color', '#b2b2b0' );
		$style2_currentstep_circle_background_color = get_option( 'yith_wcms_timeline_style2_current_bubble_background_color', '#acc327' );
		$style2_step_circle_color                   = get_option( 'yith_wcms_timeline_style2_bubble_color', '#ffffff' );
		$style2_currentstep_circle_color            = get_option( 'yith_wcms_timeline_style2_current_bubble_color', '#ffffff' );
		$style2_step_text_color                     = get_option( 'yith_wcms_timeline_style2_step_color', '#b2b2b0' );
		$style2_currentstep_text_color              = get_option( 'yith_wcms_timeline_style2_current_step_color', '#303030' );

		/* Style 3 */
		$style3_step_background_color        = get_option( 'yith_wcms_timeline_style3_background_color', 'rgba(255,255,255,0)' );
		$style3_currentstep_background_color = get_option( 'yith_wcms_timeline_style3_active_background_color', '#1e8cbe' );
		$style3_step_border_color            = get_option( 'yith_wcms_timeline_style3_border_color', '#e2e2e2' );
		$style3_step_text_color              = get_option( 'yith_wcms_timeline_style3_step_color', '#b2b2b0' );
		$style3_currentstep_text_color       = get_option( 'yith_wcms_timeline_style3_current_step_color', '#ffffff' );

		/* Style for mobile devices */
		$yith_wcms_timeline_template = get_option( 'yith_wcms_timeline_template', 'text' );

		/* Navigation Button Style */
		$yith_wcms_nav_buttons_style         = get_option( 'yith_wcms_nav_buttons_style', 'theme-style' );
		$yith_wcms_back_to_cart_button_style = get_option( 'yith_wcms_back_to_cart_button_style', 'theme-style' );

		$create_options = array(
			/* General */
			'yith_wcms_text_step_separator_onoff'               => ! empty( $step_separator ) ? 'yes' : 'no',
			'yith_wcms_show_step_number'                        => 'number' === $step_count_type ? 'yes' : 'no',

			/* Text Style */
			'yith_wcms_timeline_text_step_color'     => array(
				'prev'    => '#000000',
				'current' => '#000000',
				'future'  => '#000000',
				'hover'   => '#000000',
			),

			/* Style1 */
			'yith_wcms_timeline_style1_step_background_color'   => array(
				'prev'    => $style1_step_background_color,
				'current' => $style1_current_step_background_color,
				'future'  => $style1_step_background_color,
				'hover'   => $style1_step_background_color,
			),
			'yith_wcms_timeline_style1_step_text_color'         => array(
				'prev'    => $style1_step_text_color,
				'current' => $style1_currentstep_text_color,
				'future'  => $style1_step_text_color,
				'hover'   => $style1_step_text_color,
			),
			'yith_wcms_timeline_style1_square_background_color' => array(
				'prev'    => $style1_square_background_color,
				'current' => $style1_currentsquare_background_color,
				'future'  => $style1_square_background_color,
				'hover'   => $style1_square_background_color,
			),
			'yith_wcms_timeline_style1_square_text_color'       => array(
				'prev'    => $style1_square_text_color,
				'current' => $style1_currentsquare_text_color,
				'future'  => $style1_square_text_color,
				'hover'   => $style1_square_text_color,
			),

			/* Style2 */
			'yith_wcms_timeline_style2_step_background_color'   => array(
				'prev'    => $style2_step_background_color,
				'current' => $style2_currentstep_background_color,
				'future'  => $style2_step_background_color,
				'hover'   => $style2_step_background_color,
			),
			'yith_wcms_timeline_style2_step_text_color'         => array(
				'prev'    => $style2_step_text_color,
				'current' => $style2_currentstep_text_color,
				'future'  => $style2_step_text_color,
				'hover'   => $style2_step_text_color,
			),
			'yith_wcms_timeline_style2_step_border_color'       => array(
				'prev'    => $style2_step_border_color,
				'current' => $style2_step_border_color,
				'future'  => $style2_step_border_color,
				'hover'   => $style2_step_border_color,
			),
			'yith_wcms_timeline_style2_circle_background_color' => array(
				'prev'    => $style2_step_circle_color,
				'current' => $style2_currentstep_circle_color,
				'future'  => $style2_step_circle_color,
				'hover'   => $style2_step_circle_color,
			),
			'yith_wcms_timeline_style2_circle_text_color'       => array(
				'prev'    => $style2_step_circle_color,
				'current' => $style2_currentstep_circle_color,
				'future'  => $style2_step_circle_color,
				'hover'   => $style2_step_circle_color,
			),
			'yith_wcms_timeline_style2_circle_border_color'     => array(
				'prev'    => $style2_step_circle_color,
				'current' => $style2_currentstep_circle_color,
				'future'  => $style2_step_circle_color,
				'hover'   => $style2_step_circle_color,
			),

			/* Style 3 */
			'yith_wcms_timeline_style3_step_background_color'     => array(
				'prev'    => $style3_step_background_color,
				'current' => $style3_currentstep_background_color,
				'future'  => $style3_step_background_color,
				'hover'   => $style3_step_background_color,
			),
			'yith_wcms_timeline_style3_step_text_color'     => array(
				'prev'    => $style3_step_text_color,
				'current' => $style3_currentstep_text_color,
				'future'  => $style3_step_text_color,
				'hover'   => $style3_step_text_color,
			),
			'yith_wcms_timeline_style3_step_border_color'     => array(
				'prev'    => $style3_step_border_color,
				'current' => $style3_step_border_color,
				'future'  => $style3_step_border_color,
				'hover'   => $style3_step_border_color,
			),

			/* Style on Mobile Devices */
			'yith_wcms_timeline_template_on_mobile' => $yith_wcms_timeline_template,

			/* Text alignment for old style */
			'yith_wcms_timeline_style1_step_text_alignment' => 'center',
			'yith_wcms_timeline_style2_step_text_alignment' => 'center',
			'yith_wcms_timeline_style3_step_text_alignment' => 'center',

			/* Navigation buttons style */
			'yith_wcms_nav_buttons_style'         => $yith_wcms_nav_buttons_style,
			'yith_wcms_back_to_cart_button_style' => $yith_wcms_back_to_cart_button_style,
		);

		foreach ( $create_options as $new_option => $value ) {
			add_option( $new_option, $value );
		}

		/* Icons */

		$old_icons_backup = array();

		$steps = array(
			'login',
			'billing',
			'shipping',
			'order',
			'payment'
		);

		$old_default_icon = array(
			'login'         => YITH_WCMS_ASSETS_URL . 'images/icon/login.png',
			'billing'       => YITH_WCMS_ASSETS_URL . 'images/icon/billing.png',
			'shipping'      => YITH_WCMS_ASSETS_URL . 'images/icon/shipping.png',
			'order'         => YITH_WCMS_ASSETS_URL . 'images/icon/order.png',
			'payment'       => YITH_WCMS_ASSETS_URL . 'images/icon/payment.png',
		);

		foreach ( $steps as $step ){
			$option_icon = get_option( 'yith_wcms_timeline_options_icon_' . $step );
			$use_icon = 'default-icon';
			$old_icons_backup[ $step ] = $option_icon;

			if( empty( $option_icon ) || in_array( $option_icon, $old_default_icon ) ){
				$option_icon = $step . '2';
				update_option( 'yith_wcms_timeline_options_icon_' . $step, $option_icon );
			}

			else {
				$use_icon = 'custom-icon';
			}

			add_option( 'yith_wcms_use_icon_' . $step, $use_icon );
		}

		$old_options = array(
			'yith_wcms_timeline_step_count_type'                        => $step_count_type,
			'yith_wcms_timeline_style1_background_color'                => $style1_step_background_color,
			'yith_wcms_timeline_style1_active_background_color'         => $style1_current_step_background_color,
			'yith_wcms_timeline_style1_step_label_color'                => $style1_step_text_color,
			'yith_wcms_timeline_style1_current_step_label_color'        => $style1_currentstep_text_color,
			'yith_wcms_timeline_style1_current_step_background_color'   => $style1_square_background_color,
			'yith_wcms_timeline_style1_step_color'                      => $style1_currentsquare_background_color,
			'yith_wcms_timeline_style1_current_step_color'              => $style1_square_text_color,
			'yith_wcms_timeline_style1_step_background_color_temp'      => $style1_currentsquare_text_color,
			'yith_wcms_timeline_style2_border_color'                    => $style2_step_border_color,
			'yith_wcms_timeline_style2_background_color'                => $style2_step_background_color,
			'yith_wcms_timeline_style2_active_background_color'         => $style2_currentstep_background_color,
			'yith_wcms_timeline_style2_bubble_background_color'         => $style2_step_circle_background_color,
			'yith_wcms_timeline_style2_current_bubble_background_color' => $style2_currentstep_circle_background_color,
			'yith_wcms_timeline_style2_bubble_color'                    => $style2_step_circle_color,
			'yith_wcms_timeline_style2_current_bubble_color'            => $style2_currentstep_circle_color,
			'yith_wcms_timeline_style2_step_color'                      => $style2_step_text_color,
			'yith_wcms_timeline_style2_current_step_color'              => $style2_currentstep_text_color,
			'yith_wcms_timeline_style3_background_color'                => $style3_step_background_color,
			'yith_wcms_timeline_style3_active_background_color'         => $style3_currentstep_background_color,
			'yith_wcms_timeline_style3_border_color'                    => $style3_step_border_color,
			'yith_wcms_timeline_style3_step_color'                      => $style3_step_text_color,
			'yith_wcms_timeline_style3_current_step_color'              => $style3_currentstep_text_color,
			'yith_wcms_old_icons'                                       => $old_icons_backup
		);

		foreach ( $old_options as $option_id => $value ){
			delete_option( $option_id );
		}

		/**
		 * Save a backup copy of old options
		 * if something goes wrong we can
		 * restore it.
		 */
		update_option( 'yith_wcms_old_options_backup', $old_options );

		update_option( 'yith_wcms_db_version', '2.0.0' );
	}
}

function yith_wcms_update_db_2_0_1() {
	$db_version = get_option( 'yith_wcms_db_version', '1.0.0' );
	if ( $db_version && version_compare( $db_version, '2.0.1', '<' ) ) {
		/**
		 * Remove the old options backup
		 */
		delete_option( 'yith_wcms_old_options_backup');
		update_option( 'yith_wcms_db_version', '2.0.1' );
	}
}

add_action( 'admin_init', 'yith_wcms_update_db_1_0_1' );
add_action( 'admin_init', 'yith_wcms_update_db_1_0_2' );
add_action( 'admin_init', 'yith_wcms_update_db_1_0_3' );
add_action( 'admin_init', 'yith_wcms_update_db_2_0_0' );
//add_action( 'admin_init', 'yith_wcms_update_db_2_0_1' );
