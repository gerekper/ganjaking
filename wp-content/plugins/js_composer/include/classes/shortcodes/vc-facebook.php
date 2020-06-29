<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPBakeryShortCode_Vc_Facebook
 */
class WPBakeryShortCode_Vc_Facebook extends WPBakeryShortCode {
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
		 * @var $type
		 * @var $el_class
		 * @var $css
		 * @var $css_animation
		 * Shortcode class
		 * @var WPBakeryShortCode_Vc_Facebook $this
		 */
		$type = $css = $el_class = '';
		$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
		extract( $atts );

		$url = get_permalink();

		$css = isset( $atts['css'] ) ? $atts['css'] : '';
		$el_class = isset( $atts['el_class'] ) ? $atts['el_class'] : '';

		$class_to_filter = 'wpb_googleplus vc_social-placeholder wpb_content_element vc_socialtype-' . $type;
		$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

		return '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $css_class ) . '"></a>';
	}
}
