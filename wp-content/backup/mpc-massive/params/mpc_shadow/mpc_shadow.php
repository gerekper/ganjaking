<?php
/*----------------------------------------------------------------------------*\
	MPC_SHADOW Param
\*----------------------------------------------------------------------------*/

vc_add_shortcode_param( 'mpc_shadow', 'mpc_shadow_settings', mpc_get_plugin_path( __FILE__ ) . '/assets/js/mpc-params.js' );
function mpc_shadow_settings( $settings, $value ) {
	$defaults = array(
		'value' => ''
	);
	$settings = wp_parse_args( $settings, $defaults );
	$value    = $value == null ? $settings[ 'value' ] : $value;

	if ( $value != '' ) {
		$value = preg_replace( '/\s+/', ' ', trim( $value ) );
		$shadow_values = explode( ' ', $value, 4 );

		if ( count( $shadow_values ) == 4 ) {
			$left  = (int) $shadow_values[ 0 ];
			$top   = (int) $shadow_values[ 1 ];
			$blur  = (int) $shadow_values[ 2 ];
			$color = preg_replace( '/\s+/', '', $shadow_values[ 3 ] );
		} else {
			$color = '';
		}
	} else {
		$color = '';
	}

	if ( $color == '' || ( ( $left == '' || $left == 0 ) && ( $top == '' || $top == 0 ) && ( $blur == '' || $blur == 0 ) ) ) {
		$value = '';
		$shadow = 'text-shadow: none';

		$top = $left = $blur = $color = '';
	} else {
		$shadow = 'text-shadow: ' . esc_attr( $left ) . 'px ' . esc_attr( $top ) . 'px ' . esc_attr( $blur ) . 'px ' . esc_attr( $color );
	}

	$output = '<div class="vc_row">';

		$output .= '<div class="vc_col-sm-2 vc_column"><div class="mpc-text-input" data-validate="true"><p>' . __( 'Left Offset', 'mpc' ) . '</p><input name="mpc-shadow-left" class="mpc-shadow-left mpc_text_field" type="number" value="' . esc_attr( $left ) . '"><label>px</label></div></div>';
		$output .= '<div class="vc_col-sm-2 vc_column"><div class="mpc-text-input" data-validate="true"><p>' . __( 'Top Offset', 'mpc' ) . '</p><input name="mpc-shadow-top" class="mpc-shadow-top mpc_text_field" type="number" value="' . esc_attr( $top ) . '"><label>px</label></div></div>';
		$output .= '<div class="vc_col-sm-2 vc_column"><div class="mpc-text-input" data-validate="true"><p>' . __( 'Blur', 'mpc' ) . '</p><input name="mpc-shadow-blur" class="mpc-shadow-blur mpc_text_field" type="number" min="0" value="' . esc_attr( $blur ) . '"><label>px</label></div></div>';

		$output .= '<div class="vc_col-sm-6 vc_column"><p>' . __( 'Color', 'mpc' ) . '</p><input class="mpc-color-picker mpc-shadow-color" data-alpha="true" data-reset-alpha="true" type="text" value="' . esc_attr( $color ). '" /></div>';

		$output .= '<div class="vc_col-sm-12 vc_column"><p class="mpc-shadow-text" style="' . $shadow . '">' . __( 'Shadow Preview', 'mpc' ) . '</p></div>';

	$output .= '</div>';

	$output .= '<input class="mpc-value wpb_vc_param_value wpb-input ' . esc_attr( $settings[ 'param_name' ] ) . '" name="' . esc_attr( $settings[ 'param_name' ] ) . '" value="' . esc_attr( $value ) . '" type="hidden" />';

	return $output;
}
