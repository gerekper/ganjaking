<?php
/*----------------------------------------------------------------------------*\
	MPC_TEXT Param
\*----------------------------------------------------------------------------*/

vc_add_shortcode_param( 'mpc_text', 'mpc_text_settings', mpc_get_plugin_path( __FILE__ ) . '/assets/js/mpc-params.js' );
function mpc_text_settings( $settings, $value ) {
	$icon_wrap = isset( $settings['addon'] )  ? '<i class="mpc-add-on dashicons '. esc_attr( $settings['addon']['icon'] ) .'"></i>' : '';
	$icon_align = isset( $settings['addon'] )  ? esc_attr( 'mpc-input-'. $settings['addon']['align'] ) : '';
	$label = isset( $settings['label'] ) ? $settings['label'] : '';
	$placeholder = isset( $settings['placeholder'] ) ? esc_attr( $settings['placeholder'] ) : '';
	$validate = isset( $settings['validate'] ) ? ' data-validate="' . esc_attr( $settings['validate'] ) . '"' : ' data-validate="true"';

	return sprintf('<div class="mpc-text-input %1$s"%8$s>%2$s <input id="%3$s" name="%3$s" class="wpb_vc_param_value %3$s %4$s_field" type="text" value="%5$s" placeholder="%7$s"><label for="%3$s">%6$s</label></div>',
		$icon_align,
		$icon_wrap,
		esc_attr( $settings['param_name'] ),
		esc_attr( $settings['type'] ),
		esc_attr( $value ),
		$label,
		$placeholder,
		$validate
	);
}
