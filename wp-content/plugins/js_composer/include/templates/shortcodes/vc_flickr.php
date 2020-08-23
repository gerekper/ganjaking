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
// https://api.flickr.com/services/feeds/photos_public.gne?id=94395039@N00&format=json&nojsoncallback=1
$provider = 'https://www.flickr.com/services/feeds/photos_public.gne';
$flickr_url = 'https://www.flickr.com/photos/' . esc_attr( $flickr_id );
if ( 'group' === $type ) {
	$provider = 'https://www.flickr.com/services/feeds/groups_pool.gne';
	$flickr_url = 'https://www.flickr.com/groups/' . esc_attr( $flickr_id ) . '/pool';
}
$url = $provider . '?id=' . $flickr_id . '&format=json&nojsoncallback=1';
$response = wp_safe_remote_get( $url );
$items = [];
if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
	$body = wp_remote_retrieve_body( $response );
	$data = json_decode( trim( $body ), true );
	$num = 0;
	foreach ( $data['items'] as $item ) {
		if ( $num > $count ) {
			break;
		}
		if ( isset( $item['media']['m'] ) ) {
			$items[] = '<div class="flickr_badge_image"><a href="' . esc_url( $item['link'] ) . '"><img src="' . esc_url( $item['media']['m'] ) . '" title="' . esc_attr( $item['title'] ) . '" /></a></div>';
		}
		$num ++;
	}
}
if ( 'random' === $display ) {
	shuffle( $items );
}
// @codingStandardsIgnoreStarts
$output = '
	<div class="' . esc_attr( $css_class ) . '" ' . implode( ' ', $wrapper_attributes ) . '>
		<div class="wpb_wrapper">
			' . wpb_widget_title( array(
		'title' => $title,
		'extraclass' => 'wpb_flickr_heading',
	) ) . '<div>' . implode( '', $items ) . '</div><p class="flickr_stream_wrap"><a class="wpb_follow_btn wpb_flickr_stream" href="' . esc_url( $flickr_url ) . '">' . esc_html__( 'View stream on flickr', 'js_composer' ) . '</a></p>
		</div>
	</div>
';

return $output;
