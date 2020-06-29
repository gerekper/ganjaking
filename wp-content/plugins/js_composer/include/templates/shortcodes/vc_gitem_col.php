<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $width
 * @var $align
 * @var $css
 * @var $el_class
 * @var $featured_image
 * @var $img_size
 * @var $content - shortcode content
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Gitem_Col $this
 */
$width = $align = $css = $el_class = $featured_image = $img_size = '';

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );

extract( $atts );
// TODO: Note that vc_map_get_attributes doesnt return align so it should be checked in next bug fix

$style = '';
$width = wpb_translateColumnWidthToSpan( $width );
$css_class = $width . ( strlen( $el_class ) ? ' ' . $el_class : '' ) . ' vc_gitem-col vc_gitem-col-align-' . $align . vc_shortcode_custom_css_class( $css, ' ' );

if ( 'yes' === $featured_image ) {
	$style = '{{ post_image_background_image_css' . ':' . esc_attr( $img_size ) . ' }}';
}
$output = '<div class="' . esc_attr( $css_class ) . '"' . ( strlen( $style ) > 0 ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>' . do_shortcode( $content ) . '</div>';

echo $output;
