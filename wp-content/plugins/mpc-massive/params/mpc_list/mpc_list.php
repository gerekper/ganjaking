<?php
/*----------------------------------------------------------------------------*\
	MPC_LIST Param
\*----------------------------------------------------------------------------*/

vc_add_shortcode_param( 'mpc_list', 'mpc_list_settings', mpc_get_plugin_path( __FILE__ ) . '/assets/js/mpc-params.js' );
function mpc_list_settings( $settings, $value ) {
	if ( empty( $settings[ 'options' ] ) ) {
		return '';
	}

	$active_items = array();
	if ( $value != '' ) {
		$active_items = explode( ',', $value );
	}

	$list = '<div class="mpc-vc-list-wrap">';
		$list .= '<div class="mpc-vc-list">';
		foreach ( $settings[ 'options' ] as $option => $option_name ) {
			$option_id = $settings[ 'param_name' ] . '-option_' . $option;

			$list .= '<input id="' . esc_attr( $option_id ) . '" class="mpc-list-option" name="' . esc_attr( $settings[ 'param_name' ] ) . '-option_' . esc_attr( $option ) . '" type="checkbox" ' . ( in_array( $option, $active_items ) ? 'checked="checked"' : '' ) . ' data-name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $option ) . '" />';
			$list .= '<label class="mpc-list-single mpc-list-' . esc_attr( $option ) . '" for="' . esc_attr( $option_id ) . '">' . $option_name . '</label>';
		}
		$list .= '</div>';
		$list .= '<div class="mpc-vc-list-order">';
		foreach ( $active_items as $item ) {
			$list .= '<div class="mpc-list-item mpc-list-' . esc_attr( $item ) . '" data-id="' . esc_attr( $item ) . '"><i class="dashicons dashicons-sort"></i>' . $settings[ 'options' ][ $item ] . '</div>';
		}
		$list .= '</div>';
		$list .= '<input class="mpc-value wpb_vc_param_value" name="' . esc_attr( $settings[ 'param_name' ] ) . '" value="' . esc_attr( $value ) . '" type="hidden" />';
	$list .= '</div>';

	return $list;
}
