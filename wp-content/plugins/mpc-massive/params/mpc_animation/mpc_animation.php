<?php
/*----------------------------------------------------------------------------*\
	MPC_ANIMATION Param
\*----------------------------------------------------------------------------*/

vc_add_shortcode_param( 'mpc_animation', 'mpc_animation_settings', mpc_get_plugin_path( __FILE__ ) . '/assets/js/mpc-params.js' );
function mpc_animation_settings( $settings, $value ) {
	$defaults = array(
		'value' => array( '' => '' ),
	);

	$settings = wp_parse_args( $settings, $defaults );

	if ( ! is_array( $settings[ 'value' ] ) ) {
		return;
	}

	$animations_list = '<select name="' . esc_attr( $settings[ 'param_name' ] ) . '" class="mpc-vc-animation wpb_vc_param_value wpb-input wpb-select ' . esc_attr(  $settings[ 'param_name' ] ) . ' dropdown" data-option="' . esc_attr( $value ) . '">';
	foreach ( $settings[ 'value' ] as $name => $type ) {
		$animations_list .= '<option class="' . esc_attr( $type ) . '" value="' . esc_attr( $type ) . '" ' . selected( $type, $value, false ) . '>' . $name . '</option>';
	}
	$animations_list .= '</select>';

	$reply_button = '<a class="mpc-animation-replay mpc-action button" href="#replay"><i class="dashicons dashicons-update"></i></a>';
	$preview_box  = '<div class="mpc-box"><div class="mpc-inner-box">' . __( 'Preview', 'mpc' ) . '</div></div>';

	return $animations_list . $reply_button . $preview_box;
}
