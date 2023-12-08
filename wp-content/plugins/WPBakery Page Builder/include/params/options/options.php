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
function vc_options_form_field( $settings, $value ) {
	return sprintf( '<div class="vc_options"><input name="%s" class="wpb_vc_param_value  %s_field" type="hidden" value="%s"/><a href="#" class="button vc_options-edit %s_button">%s</a></div><div class="vc_options-fields" data-settings="%s"><a href="#" class="button vc_close-button">%s</a></div>', esc_attr( $settings['param_name'] ), esc_attr( $settings['param_name'] . ' ' . $settings['type'] ), $value, esc_attr( $settings['param_name'] ), esc_html__( 'Manage options', 'js_composer' ), htmlspecialchars( wp_json_encode( $settings['options'] ) ), esc_html__( 'Close', 'js_composer' ) );
}

/**
 * @since 4.2
 */
function vc_options_include_templates() {
	require_once vc_path_dir( 'TEMPLATES_DIR', 'params/options/templates.html' );
}

add_action( 'admin_footer', 'vc_options_include_templates' );
