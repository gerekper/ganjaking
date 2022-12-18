<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $title
 * @var $el_class
 * @var $style : removed
 * @var $color : removed
 * @var $size : removed
 * @var $open
 * @var $css_animation
 * @var $el_id
 * @var $content - shortcode content
 * @var $css
 *
 * Extra Params
 * @var $show_icon
 * @var $icon_type
 * @var $icon_image
 * @var $icon
 * @var $icon_simpleline
 *
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Toggle
 */
$output = '';

$inverted = false;
$atts     = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$el_class = porto_shortcode_extract_class( $el_class );

$class_to_filter = vc_shortcode_custom_css_class( $css, ' ' );
$css_class       = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

$css_class .= ( 'true' == $open ) ? ' active' : '';

$css_class  = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'toggle ' . $css_class, $this->settings['base'] );
$css_class .= $this->getCSSAnimation( $css_animation ) . ' ' . $el_class;

switch ( $icon_type ) {
	case 'simpleline':
		$icon_class = $icon_simpleline;
		break;
	case 'image':
		$icon_class = 'icon-image';
		break;
	default:
		$icon_class = $icon;
}
if ( ! $show_icon ) {
	$icon_class = '';
}

$output .= '<section class="' . esc_attr( $css_class ) . '">';
$output .= '<label>';
if ( $icon_class ) {
	$output .= '<i class="' . esc_attr( $icon_class ) . '">';
	if ( 'icon-image' == $icon_class && $icon_image ) {
		$icon_image = preg_replace( '/[^\d]/', '', $icon_image );
		$image_url  = wp_get_attachment_url( $icon_image );
		$alt_text   = get_post_meta( $icon_image, '_wp_attachment_image_alt', true );
		$image_url  = str_replace( array( 'http:', 'https:' ), '', $image_url );
		if ( $image_url ) {
			$output .= '<img alt="' . esc_attr( $alt_text ) . '" src="' . esc_url( $image_url ) . '">';
		}
	}
	$output .= '</i>';
}
$output .= $title . '</label>';
$output .= '<div class="toggle-content">' . wpb_js_remove_wpautop( $content, true ) . '</div>';
$output .= '</section>';

echo porto_filter_output( $output );
