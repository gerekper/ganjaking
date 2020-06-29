<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 * @var $link
 * @var $title
 * @var $color
 * @var $size
 * @var $style
 * @var $el_class
 * @var $align
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Button2 $this
 */
$link = $title = $color = $size = $style = $el_class = $align = '';
$wrapper_start = $wrapper_end = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$class = 'vc_btn';
// parse link
$link = ( '||' === $link ) ? '' : $link;
$link = vc_build_link( $link );
$a_href = $link['url'];
$a_title = $link['title'];
$a_target = $link['target'];
$a_rel = $link['rel'];
if ( ! empty( $a_rel ) ) {
	$a_rel = ' rel="' . esc_attr( trim( $a_rel ) ) . '"';
}

$class .= ( '' !== $color ) ? ( ' vc_btn_' . $color . ' vc_btn-' . $color ) : '';
$class .= ( '' !== $size ) ? ( ' vc_btn_' . $size . ' vc_btn-' . $size ) : '';
$class .= ( '' !== $style ) ? ' vc_btn_' . $style : '';

$el_class = $this->getExtraClass( $el_class );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, ' ' . $class . $el_class, $this->settings['base'], $atts );
$wrapper_css_class = 'vc_button-2-wrapper';
if ( $align ) {
	$wrapper_css_class .= ' vc_button-2-align-' . $align;
}
$output = '';

$output .= '
<div class="' . esc_attr( $wrapper_css_class ) . '"><a class="' . esc_attr( trim( $css_class ) ) . '" href="' . esc_attr( $a_href ) . '" title="' . esc_attr( $a_title ) . '" target="' . esc_attr( $a_target ) . '"' . ( ! empty( $a_rel ) ? ' rel="' . esc_attr( trim( $a_rel ) ) . '"' : '' ) . '>';
$output .= $title;
$output .= '</a></div>';

return $output;
