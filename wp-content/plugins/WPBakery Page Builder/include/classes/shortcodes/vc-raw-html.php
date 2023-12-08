<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 */
class WPBakeryShortCode_Vc_Raw_Html extends WPBakeryShortCode {

	/**
	 * @param $param
	 * @param $value
	 * @return string
	 */
	public function singleParamHtmlHolder( $param, $value ) {
		$output = '';
		// Compatibility fixes
		// TODO: check $old_names & &new_names. Leftover from copypasting?
		$old_names = array(
			'yellow_message',
			'blue_message',
			'green_message',
			'button_green',
			'button_grey',
			'button_yellow',
			'button_blue',
			'button_red',
			'button_orange',
		);
		$new_names = array(
			'alert-block',
			'alert-info',
			'alert-success',
			'btn-success',
			'btn',
			'btn-info',
			'btn-primary',
			'btn-danger',
			'btn-warning',
		);
		$value = str_ireplace( $old_names, $new_names, $value );

		$param_name = isset( $param['param_name'] ) ? $param['param_name'] : '';
		$type = isset( $param['type'] ) ? $param['type'] : '';
		$class = isset( $param['class'] ) ? $param['class'] : '';

		if ( isset( $param['holder'] ) && 'hidden' !== $param['holder'] ) {
			if ( 'textarea_raw_html' === $param['type'] ) {
				// @codingStandardsIgnoreLine
				$output .= sprintf( '<%s class="wpb_vc_param_value %s %s %s" name="%s">%s</%s><input type="hidden" name="%s_code" class="%s_code" value="%s" />', $param['holder'], $param_name, $type, $class, $param_name, htmlentities( rawurldecode( base64_decode( wp_strip_all_tags( $value ) ) ), ENT_COMPAT, 'UTF-8' ), $param['holder'], $param_name, $param_name, wp_strip_all_tags( $value ) );

			} else {
				$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
			}
		}

		return $output;
	}
}
