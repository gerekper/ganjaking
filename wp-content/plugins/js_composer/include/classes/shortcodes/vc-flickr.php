<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPBakeryShortCode_Vc_flickr
 */
class WPBakeryShortCode_Vc_Flickr extends WPBakeryShortCode {
	/**
	 * @param $atts
	 * @param null $content
	 * @return string
	 * @throws \Exception
	 */
	protected function contentInline( $atts, $content = null ) {
		/**
		 * Shortcode attributes
		 * @var $atts
		 * @var $el_class
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
		$title = '';
		$flickr_id = '';
		$css_animation = '';
		$count = '';
		$type = '';
		$display = '';
		$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
		extract( $atts );

		$css = isset( $atts['css'] ) ? $atts['css'] : '';
		$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

		$class_to_filter = 'wpb_flickr_widget wpb_content_element';
		$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

		$params = array(
			'title' => $title,
			'extraclass' => 'wpb_flickr_heading',
		);
		$output = sprintf( '
			<div class="%s">
				<div class="wpb_wrapper">
					%s
					<div class="vc_flickr-inline-placeholder" data-link="https://www.flickr.com/badge_code_v2.gne?count=%s&amp;display=%s&amp;size=s&amp;layout=x&amp;source=%s&amp;%s=%s"></div>
					<p class="flickr_stream_wrap"><a class="wpb_follow_btn wpb_flickr_stream" href="https://www.flickr.com/photos/%s">%s</a></p>
				</div>
			</div>', $css_class, wpb_widget_title( $params ), $count, $display, $type, $type, $flickr_id, $flickr_id, esc_html__( 'View stream on flickr', 'js_composer' ) );

		return $output;
	}
}
