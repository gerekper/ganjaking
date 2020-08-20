<?php
/*----------------------------------------------------------------------------*\
	MPC_SLIDER Param
\*----------------------------------------------------------------------------*/

vc_add_shortcode_param( 'mpc_slider', 'mpc_slider_settings', mpc_get_plugin_path( __FILE__ ) . '/assets/js/mpc-params.js' );
function mpc_slider_settings( $settings, $value ) {
	$defaults = array(
		'min'        => 0,
		'max'        => 100,
		'step'       => 1,
		'value'      => 0,
		'unit'       => '',
		'fill'       => true,
		'hide_input' => false
	);
	$settings = wp_parse_args( $settings, $defaults );
	$value = $value == null ? $settings[ 'value' ] : $value;

	$slider = '<div class="mpc-vc-slider' . ( $settings[ 'hide_input' ] ? ' mpc-hide-input' : '' ) . ( $settings[ 'fill' ] ? ' mpc-fill' : '' ) . '">';
		$slider .= '<div class="mpc-slider" data-min="' . esc_attr( $settings[ 'min' ] ) . '" data-max="' . esc_attr( $settings[ 'max' ] ) . '" data-step="' . esc_attr( $settings[ 'step' ] ) . '" data-value="' . esc_attr( $value ) . '"></div>';
	$slider .= '</div>';

	$input = '<input class="mpc-value wpb_vc_param_value wpb-input ' . esc_attr( $settings[ 'param_name' ] ) . '" name="' . esc_attr( $settings[ 'param_name' ] ) . '" value="' . esc_attr( $value ) . '" type="text" />';

	$unit = $settings[ 'unit' ] != '' ? '<span class="mpc-unit">' . $settings[ 'unit' ] . '</span>' : '';

	return '<div class="mpc-slider-wrap">' . $slider . $unit . $input . '</div>';
}
