<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 * @var $type
 * @var $icon_fontawesome
 * @var $icon_openiconic
 * @var $icon_typicons
 * @var $icon_entypo
 * @var $icon_linecons
 * @var $color
 * @var $custom_color
 * @var $background_style
 * @var $background_color
 * @var $custom_background_color
 * @var $size
 * @var $align
 * @var $el_class
 * @var $el_id
 * @var $link
 * @var $css_animation
 * @var $css
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Icon $this
 */
$type = $icon_fontawesome = $icon_openiconic = $icon_typicons = $icon_entypo = $icon_linecons = $color = $custom_color = $background_style = $background_color = $custom_background_color = $size = $align = $el_class = $el_id = $link = $css_animation = $css = $rel = '';

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$class_to_filter = '';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

// Enqueue needed icon font.
vc_icon_element_fonts_enqueue( $type );

$url = vc_build_link( $link );
$has_style = false;
if ( strlen( $background_style ) > 0 ) {
	$has_style = true;
	if ( false !== strpos( $background_style, 'outline' ) ) {
		$background_style .= ' vc_icon_element-outline'; // if we use outline style it is border in css
	} else {
		$background_style .= ' vc_icon_element-background';
	}
}

$iconClass = isset( ${'icon_' . $type} ) ? esc_attr( ${'icon_' . $type} ) : 'fa fa-adjust';

$style = '';
if ( 'custom' === $background_color ) {
	if ( false !== strpos( $background_style, 'outline' ) ) {
		$style = 'border-color:' . $custom_background_color;
	} else {
		$style = 'background-color:' . $custom_background_color;
	}
}
$style = $style ? ' style="' . esc_attr( $style ) . '"' : '';
$rel = '';
if ( ! empty( $url['rel'] ) ) {
	$rel = ' rel="' . esc_attr( $url['rel'] ) . '"';
}
$output = '';
$output .= '<div' . ( ! empty( $el_id ) ? ' id="' . esc_attr( $el_id ) . '"' : '' ) . ' class="vc_icon_element vc_icon_element-outer' . ( strlen( $css_class ) > 0 ? ' ' . esc_attr( trim( $css_class ) ) : '' ) . ' vc_icon_element-align-' . esc_attr( $align );
if ( $has_style ) {
	$output .= ' vc_icon_element-have-style';
}
$output .= '"><div class="vc_icon_element-inner vc_icon_element-color-' . esc_attr( $color );
if ( $has_style ) {
	$output .= ' vc_icon_element-have-style-inner';
}
$output .= ' vc_icon_element-size-' . esc_attr( $size ) . ' vc_icon_element-style-' . esc_attr( $background_style ) . ' vc_icon_element-background-color-' . esc_attr( $background_color ) . '" ' . $style . '><span class="vc_icon_element-icon ' . esc_attr( $iconClass ) . '" ' . ( 'custom' === $color ? 'style="color:' . esc_attr( $custom_color ) . ' !important"' : '' ) . '></span>';

if ( strlen( $link ) > 0 && strlen( $url['url'] ) > 0 ) {
	$output .= '<a class="vc_icon_element-link" href="' . esc_url( $url['url'] ) . '" ' . $rel . ' title="' . esc_attr( $url['title'] ) . '" target="' . ( strlen( $url['target'] ) > 0 ? esc_attr( $url['target'] ) : '_self' ) . '"></a>';
}
$output .= '</div></div>';

return $output;
