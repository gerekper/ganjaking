<?php

namespace GroovyMenu;

use \Ultimate_VC_Addons as Ultimate_VC_Addons;

defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

if ( class_exists( '\Ultimate_VC_Addons' ) ) {

	class GrooniUltimateVCAddons extends Ultimate_VC_Addons {
		public function __construct() {
			if ( ! defined( 'UAVC_DIR' ) ) {
				define( 'UAVC_DIR', plugin_dir_path( trailingslashit( WP_PLUGIN_DIR ) . 'Ultimate_VC_Addons/Ultimate_VC_Addons.php' ) );
			}
			if ( ! defined( 'UAVC_URL' ) ) {
				define( 'UAVC_URL', plugins_url( '/', trailingslashit( WP_PLUGIN_DIR ) . 'Ultimate_VC_Addons/Ultimate_VC_Addons.php' ) );
			}
			$this->vc_template_dir = UAVC_DIR . 'vc_templates/';
			$this->vc_dest_dir     = get_template_directory() . '/vc_templates/';
			$this->module_dir      = UAVC_DIR . 'modules/';
			$this->params_dir      = UAVC_DIR . 'params/';
			$this->assets_js       = UAVC_URL . 'assets/js/';
			$this->assets_css      = UAVC_URL . 'assets/css/';
			$this->admin_js        = UAVC_URL . 'admin/js/';
			$this->admin_css       = UAVC_URL . 'admin/css/';

			$this->paths          = wp_upload_dir();
			$this->paths['fonts'] = 'smile_fonts';

			$scheme = is_ssl() ? 'https' : 'http';

			$this->paths['fonturl'] = set_url_scheme( $this->paths['baseurl'] . '/' . $this->paths['fonts'], $scheme );
		}
	}
}
