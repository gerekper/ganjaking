<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Helper function to register new shortcode attribute hook.
 *
 * @param $name - attribute name
 * @param $form_field_callback - hook, will be called when settings form is shown and attribute added to shortcode
 *     param list
 * @param $script_url - javascript file url which will be attached at the end of settings form.
 *
 * @return bool
 * @deprecated due to without prefix name 4.4
 * @since 4.2
 */
function add_shortcode_param( $name, $form_field_callback, $script_url = null ) {
	_deprecated_function( 'add_shortcode_param', '4.4 (will be removed in 6.0)', 'vc_add_shortcode_param' );

	return vc_add_shortcode_param( $name, $form_field_callback, $script_url );
}

/**
 * @return mixed|string
 * @since 4.2
 * @deprecated 4.2
 */
function get_row_css_class() {
	_deprecated_function( 'get_row_css_class', '4.2 (will be removed in 6.0)' );
	$custom = vc_settings()->get( 'row_css_class' );

	return ! empty( $custom ) ? $custom : 'vc_row-fluid';
}

/**
 * @return string
 * @deprecated 5.2
 */
function vc_generate_dependencies_attributes() {
	_deprecated_function( 'vc_generate_dependencies_attributes', '5.1', '' );

	return '';
}

/**
 * Extract width/height from string
 *
 * @param string $dimensions WxH
 * @return mixed array(width, height) or false
 * @since 4.7
 *
 * @deprecated since 5.8
 */
function vcExtractDimensions( $dimensions ) {
	_deprecated_function( 'vcExtractDimensions', '5.8', 'vc_extract_dimensions' );

	return vc_extract_dimensions( $dimensions );
}

/**
 * @param array $images IDs or srcs of images
 * @return string
 * @since 4.2
 * @deprecated since 2019, 5.8
 */
function fieldAttachedImages( $images = array() ) {
	_deprecated_function( 'fieldAttachedImages', '5.8', 'vc_field_attached_images' );

	return vc_field_attached_images( $images );
}

/**
 * @param string $asset
 *
 * @return array|string
 * @deprecated
 */
function getVcShared( $asset = '' ) {

	return vc_get_shared( $asset );
}

/**
 * Return a action param for ajax
 * @return bool
 * @since 4.8
 * @deprecated 6.1
 */
function vc_wp_action() {
	_deprecated_function( 'vc_wp_action', '6.1', 'vc_request_param' );

	return vc_request_param( 'action' );
}
