<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/** @var WPBakeryShortCode_Vc_Icon $this */
$icon = $color = $size = $align = $el_class = $custom_color = $link = $background_style = $background_color = $type = $icon_fontawesome = $icon_openiconic = $icon_typicons = $icon_entypoicons = $icon_linecons = $custom_background_color = '';

/** @var array $atts - shortcode attributes */
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );
$link = vc_gitem_create_link( $atts, 'vc_icon_element-link' );

$class_to_filter = $this->getCSSAnimation( $css_animation );
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

// Enqueue needed icon font.
vc_icon_element_fonts_enqueue( $type );

$has_style = false;
if ( strlen( $background_style ) > 0 ) {
	$has_style = true;
	if ( false !== strpos( $background_style, 'outline' ) ) {
		$background_style .= ' vc_icon_element-outline'; // if we use outline style it is border in css
	} else {
		$background_style .= ' vc_icon_element-background';
	}
}

$style = '';
if ( 'custom' === $background_color ) {
	if ( false !== strpos( $background_style, 'outline' ) ) {
		$style = 'border-color:' . $custom_background_color;
	} else {
		$style = 'background-color:' . $custom_background_color;
	}
}
$style = $style ? 'style="' . esc_attr( $style ) . '"' : '';

$output = '';
$output .= '<div class="vc_icon_element vc_icon_element-outer' . esc_attr( $css_class ) . '  vc_icon_element-align-' . esc_attr( $align );

if ( $has_style ) {
	$output .= 'vc_icon_element-have-style';
}
$output .= '">';
$output .= '<div class="vc_icon_element-inner vc_icon_element-color-' . esc_attr( $color ) . ' ';
if ( $has_style ) {
	$output .= 'vc_icon_element-have-style-inner';
}
$output .= ' vc_icon_element-size-' . esc_attr( $size ) . '  vc_icon_element-style-' . esc_attr( $background_style ) . ' vc_icon_element-background-color-' . esc_attr( $background_color ) . '" ' . $style . '><span class="vc_icon_element-icon ' . esc_attr( ${'icon_' . $type} ) . '" ' . ( 'custom' === $color ? 'style="color:' . esc_attr( $custom_color ) . ' !important"' : '' ) . '></span>';
if ( strlen( $link ) > 0 ) {
	$output .= '<' . $link . '></a>';
}
$output .= '</div></div>';

return $output;
