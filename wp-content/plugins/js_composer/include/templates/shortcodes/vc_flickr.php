<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $el_class
 * @var $el_id
 * @var $title
 * @var $flickr_id
 * @var $count
 * @var $type
 * @var $display
 * @var $css
 * @var $css_animation
 * Shortcode class
 * @var WPBakeryShortCode_Vc_flickr $this
 */
$el_class = $el_id = $title = $flickr_id = $css = $css_animation = $count = $type = $display = '';
$output = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$class_to_filter = 'wpb_flickr_widget wpb_content_element';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );
$wrapper_attributes = array();
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}
$custom_tag = 'script';
// @codingStandardsIgnoreStarts
$output = '
	<div class="' . esc_attr( $css_class ) . '" ' . implode( ' ', $wrapper_attributes ) . '>
		<div class="wpb_wrapper">
			' . wpb_widget_title( array(
		'title' => $title,
		'extraclass' => 'wpb_flickr_heading',
	) ) . '<' . $custom_tag . ' src="https://www.flickr.com/badge_code_v2.gne?count=' . esc_attr( $count ) . '&amp;display=' . esc_attr( $display ) . '&amp;size=s&amp;layout=x&amp;source=' . esc_attr( $type ) . '&amp;' . esc_attr( $type ) . '=' . esc_attr( $flickr_id ) . '"></' . $custom_tag . '>
			<p class="flickr_stream_wrap"><a class="wpb_follow_btn wpb_flickr_stream" href="https://www.flickr.com/photos/' . esc_attr( $flickr_id ) . '">' . esc_html__( 'View stream on flickr', 'js_composer' ) . '</a></p>
		</div>
	</div>
';

return $output;
