<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Params preset shortcode attribute type generator.
 *
 * Allows to set list of attributes which will be
 *
 * @param $settings
 * @param $value
 *
 * @return string - html string.
 * @since 4.4
 */
function vc_params_preset_form_field( $settings, $value ) {
	$output = '';
	$output .= '<select name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value vc_params-preset-select ' . esc_attr( $settings['param_name'] . ' ' . $settings['type'] ) . '">';
	foreach ( $settings['options'] as $option ) {
		$selected = '';
		if ( isset( $option['value'] ) ) {
			$option_value_string = (string) $option['value'];
			$value_string = (string) $value;
			if ( '' !== $value && $option_value_string === $value_string ) {
				$selected = 'selected';
			}
			$output .= '<option class="vc_params-preset-' . esc_attr( $option['value'] ) . '" value="' . esc_attr( $option['value'] ) . '" ' . $selected . ' data-params="' . esc_attr( wp_json_encode( $option['params'] ) ) . '">' . esc_html( isset( $option['label'] ) ? $option['label'] : $option['value'] ) . '</option>';
		}
	}
	$output .= '</select>';

	return $output;
}
