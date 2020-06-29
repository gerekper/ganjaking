<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $el_class
 * Shortcode class
 * @var WPBakeryShortCode $this
 */
$el_class = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$css = $this->getExtraClass( $el_class );

$output = '<div class="vc_items' . esc_attr( $css ) . '">' . esc_html__( 'Item', 'js_composer' ) . '</div>';

return $output;
