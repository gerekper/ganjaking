<?php
/*----------------------------------------------------------------------------*\
	MPC_CSS Param
\*----------------------------------------------------------------------------*/

vc_add_shortcode_param( 'mpc_css', 'mpc_css_settings', mpc_get_plugin_path( __FILE__ ) . '/assets/js/mpc-params.js' );
function mpc_css_settings( $settings, $value ) {
	$name = explode( '__', $settings[ 'param_name' ], 2 );
	if ( count( $name ) == 2 ) {
		$settings[ 'prefix' ] = $name[ 0 ] . '__' . $settings[ 'prefix' ];
	}

	return '<input class="mpc-vc-css wpb_vc_param_value" name="' . esc_attr( $settings[ 'param_name' ] ) . '" value="' . esc_attr( $value ) . '" data-prefix="' . esc_attr( $settings[ 'prefix' ] ) . '" data-section="' . esc_attr( $settings[ 'section' ] ) . '" type="text" />';
}
