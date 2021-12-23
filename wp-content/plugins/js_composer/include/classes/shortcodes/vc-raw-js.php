<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-raw-html.php' );

/**
 * Class WPBakeryShortCode_Vc_Raw_Js
 */
class WPBakeryShortCode_Vc_Raw_Js extends WPBakeryShortCode_Vc_Raw_html {
	/**
	 * @return mixed|string
	 */
	protected function getFileName() {
		return 'vc_raw_html';
	}

	/**
	 * @param $atts
	 * @param null $content
	 * @return string
	 */
	protected function contentInline( $atts, $content = null ) {
		$el_class = $width = $el_position = '';
		extract( shortcode_atts( array(
			'el_class' => '',
			'el_position' => '',
			'width' => '1/2',
		), $atts ) );

		$el_class = $this->getExtraClass( $el_class );
		$el_class .= ' wpb_raw_js';
		// @codingStandardsIgnoreLine
		$content = rawurldecode( base64_decode( wp_strip_all_tags( $content ) ) );
		$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_raw_code' . $el_class, $this->settings['base'], $atts );

		$output = '
			<div class="' . $css_class . '">
				<div class="wpb_wrapper">
					<textarea style="display: none;" class="vc_js_inline_holder">' . esc_attr( $content ) . '</textarea>
				</div>
			</div>
		';

		return $output;
	}
}
