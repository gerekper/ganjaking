<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $el_class
 * @var $css
 * @var $el_id
 * @var $equal_height
 * @var $content_placement
 * @var $content - shortcode content
 *
 * Extra Params
 * @var $is_sticky
 * @var $sticky_container_selector
 * @var $sticky_min_width
 * @var $sticky_top
 * @var $sticky_bottom
 * @var $sticky_active_class
 * @var $animation_type
 * @var $animation_duration
 * @var $animation_delay
 *
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Row_Inner
 */
$el_class        = $equal_height = $content_placement = $css = $el_id = $no_padding = '';
$disable_element = '';
$output          = $after_output = '';
$atts            = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$el_class    = $this->getExtraClass( $el_class );
$css_classes = array(
	'vc_row',
	'wpb_row', //deprecated
	'vc_inner',
	'vc_row-fluid',
	$el_class,
	vc_shortcode_custom_css_class( $css ),
);
if ( $no_padding ) {
	$css_classes[] = 'no-padding';
}

if ( 'yes' === $disable_element ) {
	if ( vc_is_page_editable() ) {
		$css_classes[] = 'vc_hidden-lg vc_hidden-xs vc_hidden-sm vc_hidden-md';
	} else {
		return '';
	}
}

if ( vc_shortcode_custom_css_has_property( $css, array( 'border', 'background' ) ) ) {
	$css_classes[] = 'vc_row-has-fill';
}

if ( ! empty( $atts['gap'] ) && empty( $no_padding ) ) {
	$css_classes[] = 'vc_column-gap-' . $atts['gap'];
}

if ( ! empty( $equal_height ) ) {
	$flex_row      = true;
	$css_classes[] = 'vc_row-o-equal-height';
}

if ( ! empty( $content_placement ) ) {
	$flex_row      = true;
	$css_classes[] = 'vc_row-o-content-' . $content_placement;
}

if ( ! empty( $flex_row ) ) {
	$css_classes[] = 'vc_row-flex';
}

$wrapper_attributes = array();
// build attributes for wrapper
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}

// lazy load background image
global $porto_settings_optimize;
if ( class_exists( 'Porto_Critical' ) ) {
	$preloads = Porto_Critical::get_instance()->get_preloads();
}
if ( isset( $porto_settings_optimize['lazyload'] ) && $porto_settings_optimize['lazyload'] && ! vc_is_inline() ) {
	preg_match( '/\.vc_custom_[^}]*(background-image:[^(]*([^)]*)|background:\s#[A-Fa-f0-9]{3,6}\s*url\(([^)]*))/', $css, $matches );
	if ( ! empty( $matches[2] ) || ! empty( $matches[3] ) ) {
		$image_url = ! empty( $matches[2] ) ? $matches[2] : $matches[3];
		$image_url = esc_url( trim( str_replace( array( '(', ')' ), '', $image_url ) ) );
		if ( empty( $preloads ) || ( isset( $preloads ) && is_array( $preloads ) && ! in_array( $image_url, $preloads ) ) ) {
			$wrapper_attributes[] = 'data-original="' . $image_url . '"';
			$css_classes[]        = 'porto-lazyload';
		}
	}
}

if ( isset( $wrap_container ) && $wrap_container ) {
	$css_classes[] = 'mx-0 porto-inner-container';
} elseif ( ! empty( $rtl_reverse ) && is_rtl() ) {
	$css_classes[] = 'flex-row-reverse';
}

$css_class            = preg_replace( '/\s+/', ' ', apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( array_unique( $css_classes ) ) ), $this->settings['base'], $atts ) );
$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';

if ( $animation_type ) {
	$wrapper_attributes[] = 'data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$wrapper_attributes[] = 'data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$wrapper_attributes[] = 'data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}

$output .= '<div ' . implode( ' ', $wrapper_attributes ) . '>';
if ( isset( $wrap_container ) && $wrap_container ) {
	$align_items_cls_arr = array(
		'middle' => 'align-items-center',
		'top'    => 'align-items-start',
		'bottom' => 'align-items-end',
	);
	$output             .= '<div class="porto-wrap-container container"><div class="row' . ( empty( $atts['gap'] ) || ! vc_is_inline() ? '' : ' vc_row' ) . ( $content_placement ? ' ' . $align_items_cls_arr[ $content_placement ] : '' ) . ( ! empty( $atts['gap'] ) && empty( $no_padding ) ? ' vc_column-gap-' . esc_attr( $atts['gap'] ) : '' ) . ( ! empty( $rtl_reverse ) && is_rtl() ? ' flex-row-reverse' : '' ) . '">';
}

if ( $is_sticky ) {
	$options                      = array();
	$options['containerSelector'] = $sticky_container_selector;
	$options['minWidth']          = (int) $sticky_min_width;
	$options['padding']['top']    = (int) $sticky_top;
	$options['padding']['bottom'] = (int) $sticky_bottom;
	$options['activeClass']       = $sticky_active_class;
	$options                      = json_encode( $options );

	$output .= '<div data-plugin-sticky data-plugin-options="' . esc_attr( $options ) . '">';
}

$output .= wpb_js_remove_wpautop( $content );

if ( $is_sticky ) {
	$output .= '</div>';
}

if ( isset( $wrap_container ) && $wrap_container ) {
	$output .= '</div></div>';
}

$output .= '</div>';
$output .= $after_output;


echo porto_filter_output( $output );
