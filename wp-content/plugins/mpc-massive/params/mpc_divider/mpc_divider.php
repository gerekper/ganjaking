<?php
/*----------------------------------------------------------------------------*\
	MPC_DIVIDER
\*----------------------------------------------------------------------------*/
vc_add_shortcode_param( 'mpc_divider', 'mpc_divider_settings' );
function mpc_divider_settings( $settings, $value ) {
	$title = isset( $settings[ 'title' ] ) ? '<div class="wpb_element_label mpc-vc-divider">' . $settings[ 'title' ] . '</div>' : '';
	$subtitle = isset( $settings[ 'subtitle' ] ) ? '<span class="vc_description vc_clearfix">' . $settings[ 'subtitle' ] . '</span>' : '';

	$input = '<input id="' . esc_attr( $settings[ 'param_name' ] ) . '" class="wpb_vc_param_value" name="' . esc_attr( $settings[ 'param_name' ] ) . '" value="" type="hidden">';
	if ( isset( $settings[ 'advanced' ] ) ) {
		$input = '<label class="mpc-vc-advanced-wrap mpc-advanced-field">' . __( 'Advanced Settings', 'mpc' ) . '<input id="' . esc_attr( $settings[ 'param_name' ] ) . '-true" class="wpb_vc_param_value mpc-vc-advanced ' . esc_attr( $settings[ 'param_name' ] ) . ' checkbox" name="' . esc_attr( $settings[ 'param_name' ] ) . '" ' . checked( $value, 'true', false ) . ' value="true" type="checkbox"></label>';
	}

	return $input . $title . $subtitle;
}
