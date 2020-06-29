<?php
/*----------------------------------------------------------------------------*\
	MPC_LAYOUT_SELECT Param
\*----------------------------------------------------------------------------*/

vc_add_shortcode_param( 'mpc_layout_select', 'mpc_layout_select_settings', mpc_get_plugin_path( __FILE__ ) . '/assets/js/mpc-params.js' );
function mpc_layout_select_settings( $settings, $value ) {
	$shortcode = isset( $settings[ 'shortcode' ] ) ? $settings[ 'shortcode' ] : '';

	$sizes = array( 0, 70, 100, 120, 150, 175, 230, 290 );
	$columns = isset( $settings[ 'columns' ] ) ? (int) $settings[ 'columns' ] : '';
	$layouts = isset( $settings[ 'layouts' ] ) && is_array( $settings[ 'layouts' ] ) ? $settings[ 'layouts'] : array();

	$current = 1;
	$column_sizes   = array_fill( 0, $columns, 0 );
	$column_content = array_fill( 0, $columns, '' );

	foreach( $layouts as $layout => $size ) {
		if ( $current <= $columns ) {
			$min = $current - 1;
		} else {
			$keys = array_keys( $column_sizes, min( $column_sizes ) );
			$min = $keys[ 0 ];
		}

		$checked = $value == $layout ? 'true' : 'false';

		$content = '<div class="mpc-layout-item" data-checked="' . $checked . '" data-size="' . esc_attr( $size ) . '" data-value="' . esc_attr( $layout ) . '">';
			$content .= '<img src="'. mpc_get_plugin_path( __FILE__ ) . '/assets/images/sprites/' . esc_attr( $shortcode ) . '/' . esc_attr( $layout ) . '.png" alt="' . esc_attr( ucfirst( $layout ) ) . '" />';
			$content .= '<span class="dashicons dashicons-yes"></span>';
		$content .= '</div>';

		$current++;
		$column_sizes[ $min ]   += (int) $sizes[ $size ];
		$column_content[ $min ] .= $content;
	}

	$return = '<div class="mpc-layout" data-columns="' . esc_attr( $columns ) . '">';
	for(  $i = 0; $i < $columns; $i++ ) {
		$return .= '<div class="mpc-layout-column">' . $column_content[ $i ] . '</div>';
	}
	$return .= '</div>';

	$return .= '<input name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value ' . esc_attr( $settings['param_name'] ) . ' ' . esc_attr( $settings['type'] ) . '_field hidden" type="text" value="' . esc_attr( $value ) . '" />';

	return $return;
}
