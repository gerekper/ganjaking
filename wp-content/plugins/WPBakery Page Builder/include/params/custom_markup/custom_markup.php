<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Function for rendering param in edit form (add element)
 * Parse settings from vc_map and entered values.
 * @since 4.4
 *
 * @param $settings
 * @param $value
 * @param $tag
 *
 * vc_filter: vc_custom_markup_render_filter - hook to override custom markup for field
 *
 * @return mixed rendered template for params in edit form
 *
 */
function vc_custom_markup_form_field( $settings, $value, $tag ) {
	return apply_filters( 'vc_custom_markup_render_filter', $value, $settings, $tag );
}
