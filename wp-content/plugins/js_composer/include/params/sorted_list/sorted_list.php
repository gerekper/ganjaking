<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @param $settings
 * @param $value
 *
 * @return string
 * @since 4.2
 */
function vc_sorted_list_form_field( $settings, $value ) {
	return sprintf( '<div class="vc_sorted-list"><input name="%s" class="wpb_vc_param_value  %s %s_field" type="hidden" value="%s" /><div class="vc_sorted-list-toolbar">%s</div><ul class="vc_sorted-list-container"></ul></div>', $settings['param_name'], $settings['param_name'], $settings['type'], $value, vc_sorted_list_parts_list( $settings['options'] ) );
}

/**
 * @param $list
 *
 * @return string
 * @since 4.2
 */
function vc_sorted_list_parts_list( $list ) {
	$output = '';
	foreach ( $list as $control ) {
		$output .= sprintf( '<div class="vc_sorted-list-checkbox"><label><input type="checkbox" name="vc_sorted_list_element" value="%s" data-element="%s" data-subcontrol="%s"> <span>%s</span></label></div>', $control[0], $control[0], count( $control ) > 1 ? htmlspecialchars( wp_json_encode( array_slice( $control, 2 ) ) ) : '', htmlspecialchars( $control[1] ) );
	}

	return $output;
}

/**
 * @param $value
 *
 * @return array
 * @since 4.2
 */
function vc_sorted_list_parse_value( $value ) {
	$data = array();
	$split = preg_split( '/\,/', $value );
	foreach ( $split as $v ) {
		$v_split = array_map( 'rawurldecode', preg_split( '/\|/', $v ) );
		$count = count( $v_split );
		if ( $count > 0 ) {
			$data[] = array(
				$v_split[0],
				$count > 1 ? array_slice( $v_split, 1 ) : array(),
			);
		}
	}

	return $data;
}
