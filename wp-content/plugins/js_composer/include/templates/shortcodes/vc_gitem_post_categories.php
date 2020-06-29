<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * Shortcode class
 * @var WPBakeryShortCode_Vc_Gitem_Post_Categories $this
 */

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );

return '{{ post_categories:' . http_build_query( array( 'atts' => $atts ) ) . ' }}';
