<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Shortcode attributes
 * @var $atts
 * @var $key
 * @var $el_class
 * @var $align
 * @var $label
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Gitem_Post_Meta $this
 */
$key = $el_class = $align = $label = '';
$label_html = '';

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$css_class = 'vc_gitem-post-meta-field-' . $key . ( strlen( $el_class ) ? ' ' . $el_class : '' ) . ( strlen( $align ) ? ' vc_gitem-align-' . $align : '' );
if ( strlen( $label ) ) {
	$label_html = '<span class="vc_gitem-post-meta-label">' . esc_html( $label ) . '</span>';
}
$output = '';
if ( strlen( $key ) ) {
	$output .= '<div class="' . esc_attr( $css_class ) . '">';
	$output .= $label_html;
	$output .= '{{ post_meta_value:' . esc_attr( $key ) . ' }}';
	$output .= '</div>';
}

return $output;
