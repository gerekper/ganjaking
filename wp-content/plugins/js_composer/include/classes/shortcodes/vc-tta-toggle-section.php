<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery Page Builder shortcodes
 *
 * @package WPBakeryPageBuilder
 * @since 7.0
 */

VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_Vc_Tta_Section' );

/**
 * Class WPBakeryShortCode_Vc_Tta_Toggle_Section
 * @since 7.0
 */
class WPBakeryShortCode_Vc_Tta_Toggle_Section extends WPBakeryShortCode_Vc_Tta_Section {
	/**
	 * Backend section controls.
	 * @since 7.0
	 *
	 * @var array
	 */
	protected $controls_list = array(
		'add',
		'edit',
	);

	/**
	 * Get template shortcode file name.
	 * @since 7.0
	 *
	 * @return mixed|string
	 */
	public function getFileName() {
		return 'vc_tta_toggle_section';
	}
}
