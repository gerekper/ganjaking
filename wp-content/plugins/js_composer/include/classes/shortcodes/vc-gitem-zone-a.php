<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-gitem-zone.php' );

/**
 * Class WPBakeryShortCode_Vc_Gitem_Zone_A
 */
class WPBakeryShortCode_Vc_Gitem_Zone_A extends WPBakeryShortCode_Vc_Gitem_Zone {
	public $zone_name = 'a';

	/**
	 * @return mixed|string
	 */
	protected function getFileName() {
		return 'vc_gitem_zone';
	}
}
