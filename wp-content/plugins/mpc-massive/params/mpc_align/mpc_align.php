<?php
/*----------------------------------------------------------------------------*\
	MPC_ALIGN Param
\*----------------------------------------------------------------------------*/

vc_add_shortcode_param( 'mpc_align', 'mpc_align_settings', mpc_get_plugin_path( __FILE__ ) . '/assets/js/mpc-params.js' );
function mpc_align_settings( $settings, $value ) {
	$defaults = array(
		'grid_size' => 'large',
	);

	$settings = wp_parse_args( $settings, $defaults );

	if ( $settings[ 'grid_size' ] == 'large' ) {
		$grid = array(
			'top-left',    'top-center',    'top-right',
			'middle-left', 'middle-center', 'middle-right',
			'bottom-left', 'bottom-center', 'bottom-right',
		);
	} elseif ( $settings[ 'grid_size' ] == 'medium' ) {
		$grid = array(
			'top-left',    'top-center',    'top-right',
			'bottom-left', 'bottom-center', 'bottom-right',
		);
	} else {
		$grid = array(
			'left', 'center', 'right',
		);
	}

	$align = '<div class="mpc-vc-align">';
	foreach ( $grid as $grid_item ) {
		$align_text = explode( '-', $grid_item );
		$align .= '<input id="' . esc_attr( $settings[ 'param_name' ] . '-radio_' . $grid_item ) . '" class="mpc-align-radio" name="' . esc_attr( $settings[ 'param_name' ] ) . '-radio" type="radio" ' . checked( $grid_item, $value, false ) . ' value="' . esc_attr( $grid_item ) . '" />';
		$align .= '<label class="mpc-align-single mpc-align-' . esc_attr( $grid_item ) . '" for="' . esc_attr( $settings[ 'param_name' ] . '-radio_' . $grid_item ) . '">';
			$align .= '<i class="dashicons dashicons-editor-align' . esc_attr( array_pop( $align_text ) ) . '"></i>';
		$align .= '</label>';
	}
	$align .= '</div>';
	$align .= '<input class="mpc-value wpb_vc_param_value" name="' . esc_attr( $settings[ 'param_name' ] ) . '" value="' . esc_attr( $value ) . '" type="hidden" />';

	return $align;
}
