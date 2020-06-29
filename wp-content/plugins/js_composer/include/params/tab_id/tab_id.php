<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @param $settings
 * @param $value
 *
 * @return string
 * @since 4.2
 */
function vc_tab_id_form_field( $settings, $value ) {
	$output = sprintf( '<div class="my_param_block"><input name="%s" class="wpb_vc_param_value wpb-textinput %s_field" type="hidden" value="%s" /><label>%s</label></div>', esc_attr( $settings['param_name'] ), esc_attr( $settings['param_name'] . ' ' . $settings['type'] ), $value, $value );

	return $output;
}
