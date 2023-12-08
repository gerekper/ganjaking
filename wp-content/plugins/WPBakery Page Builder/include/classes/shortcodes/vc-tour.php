<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-tabs.php' );

/**
 * Class WPBakeryShortCode_Vc_Tour
 */
class WPBakeryShortCode_Vc_Tour extends WPBakeryShortCode_Vc_Tabs {
	/**
	 * @return mixed|string
	 */
	protected function getFileName() {
		return 'vc_tabs';
	}

	/**
	 * @return string
	 */
	public function getTabTemplate() {
		return '<div class="wpb_template">' . do_shortcode( '[vc_tab title="' . esc_attr__( 'Slide', 'js_composer' ) . '" tab_id=""][/vc_tab]' ) . '</div>';
	}
}
