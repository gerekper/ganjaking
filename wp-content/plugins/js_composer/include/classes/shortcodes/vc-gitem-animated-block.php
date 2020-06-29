<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-gitem.php' );

/**
 * Class WPBakeryShortCode_Vc_Gitem_Animated_Block
 */
class WPBakeryShortCode_Vc_Gitem_Animated_Block extends WPBakeryShortCode_Vc_Gitem {
	protected static $animations = array();

	/**
	 * @return string
	 */
	public function itemGrid() {
		$output = '';
		$output .= '<div class="vc_gitem-animated-block-content-controls">' . '<ul class="vc_gitem-tabs vc_clearfix" data-vc-gitem-animated-block="tabs">' . '</ul>' . '</div>';
		$output .= '' . '<div class="vc_gitem-zone-tab vc_clearfix" data-vc-gitem-animated-block="add-a"></div>' . '<div class="vc_gitem-zone-tab vc_clearfix" data-vc-gitem-animated-block="add-b"></div>';

		return $output;
	}

	/**
	 * @param $width
	 * @param $i
	 * @return string
	 */
	public function containerHtmlBlockParams( $width, $i ) {
		return 'class="vc_gitem-animated-block-content"';
	}

	/**
	 * @return array
	 */
	public static function animations() {
		return array(
			esc_html__( 'Single block (no animation)', 'js_composer' ) => '',
			esc_html__( 'Double block (no animation)', 'js_composer' ) => 'none',
			esc_html__( 'Fade in', 'js_composer' ) => 'fadeIn',
			esc_html__( 'Scale in', 'js_composer' ) => 'scaleIn',
			esc_html__( 'Scale in with rotation', 'js_composer' ) => 'scaleRotateIn',
			esc_html__( 'Blur out', 'js_composer' ) => 'blurOut',
			esc_html__( 'Blur scale out', 'js_composer' ) => 'blurScaleOut',
			esc_html__( 'Slide in from left', 'js_composer' ) => 'slideInRight',
			esc_html__( 'Slide in from right', 'js_composer' ) => 'slideInLeft',
			esc_html__( 'Slide bottom', 'js_composer' ) => 'slideBottom',
			esc_html__( 'Slide top', 'js_composer' ) => 'slideTop',
			esc_html__( 'Vertical flip in with fade', 'js_composer' ) => 'flipFadeIn',
			esc_html__( 'Horizontal flip in with fade', 'js_composer' ) => 'flipHorizontalFadeIn',
			esc_html__( 'Go top', 'js_composer' ) => 'goTop20',
			esc_html__( 'Go bottom', 'js_composer' ) => 'goBottom20',
		);
	}
}
