<?php
/*----------------------------------------------------------------------------*\
	MPC_DATETIME Param
\*----------------------------------------------------------------------------*/

vc_add_shortcode_param( 'mpc_datetime', 'mpc_datetime_settings', mpc_get_plugin_path( __FILE__ ) . '/assets/js/mpc-params.js' );
function mpc_datetime_settings( $settings, $value ) {
	$icon_wrap = '<i class="mpc-add-on dashicons dashicons-calendar-alt"></i>';

	return sprintf('<div class="mpc-text-input mpc-datetime mpc-input-prepend">%1$s <input id="mpc-datetime" name="%2$s" class="wpb_vc_param_value %2$s %3$s_field" type="text" value="%4$s"></div>',
			$icon_wrap,
			esc_attr( $settings['param_name'] ),
			esc_attr( $settings['type'] ),
			esc_attr( $value )
	);
}
