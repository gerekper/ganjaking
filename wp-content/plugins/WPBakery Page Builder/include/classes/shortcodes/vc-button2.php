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
class WPBakeryShortCode_Vc_Button2 extends WPBakeryShortCode {
	/**
	 * @param $title
	 * @return string
	 */
	protected function outputTitle( $title ) {
		$icon = $this->settings( 'icon' );

		return '<h4 class="wpb_element_title"><span class="vc_general vc_element-icon' . ( ! empty( $icon ) ? ' ' . esc_attr( $icon ) : '' ) . '"></span></h4>';
	}
}
