<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery Page Builder shortcodes
 *
 * @package WPBakeryPageBuilder
 *
 */
class WPBakeryShortCode_Vc_Accordion extends WPBakeryShortCode {
	protected $controls_css_settings = 'out-tc vc_controls-content-widget';

	/**
	 * @param $atts
	 * @param null $content
	 * @return mixed|string
	 * @throws \Exception
	 */
	public function contentAdmin( $atts, $content = null ) {
		$width = $custom_markup = '';
		$shortcode_attributes = array( 'width' => '1/1' );
		foreach ( $this->settings['params'] as $param ) {
			if ( 'content' !== $param['param_name'] ) {
				$shortcode_attributes[ $param['param_name'] ] = isset( $param['value'] ) ? $param['value'] : null;
			} elseif ( 'content' === $param['param_name'] && null === $content ) {
				$content = $param['value'];
			}
		}
		extract( shortcode_atts( $shortcode_attributes, $atts ) );

		$elem = $this->getElementHolder( $width );

		$inner = '';
		foreach ( $this->settings['params'] as $param ) {
			$param_value = isset( ${$param['param_name']} ) ? ${$param['param_name']} : '';
			if ( is_array( $param_value ) ) {
				// Get first element from the array
				reset( $param_value );
				$first_key = key( $param_value );
				$param_value = $param_value[ $first_key ];
			}
			$inner .= $this->singleParamHtmlHolder( $param, $param_value );
		}

		$tmp = '';

		if ( isset( $this->settings['custom_markup'] ) && '' !== $this->settings['custom_markup'] ) {
			if ( '' !== $content ) {
				$custom_markup = str_ireplace( '%content%', $tmp . $content, $this->settings['custom_markup'] );
			} elseif ( '' === $content && isset( $this->settings['default_content_in_template'] ) && '' !== $this->settings['default_content_in_template'] ) {
				$custom_markup = str_ireplace( '%content%', $this->settings['default_content_in_template'], $this->settings['custom_markup'] );
			} else {
				$custom_markup = str_ireplace( '%content%', '', $this->settings['custom_markup'] );
			}
			$inner .= do_shortcode( $custom_markup );
		}
		$output = str_ireplace( '%wpb_element_content%', $inner, $elem );

		return $output;
	}
}
