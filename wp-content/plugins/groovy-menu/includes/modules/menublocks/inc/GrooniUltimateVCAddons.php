<?php

namespace GroovyMenu;

use \Ultimate_VC_Addons as Ultimate_VC_Addons;

defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

if ( class_exists( '\Ultimate_VC_Addons' ) ) {

	class GrooniUltimateVCAddons extends Ultimate_VC_Addons {
		public function __construct() {
			if ( ! defined( 'UAVC_DIR' ) ) {
				define( 'UAVC_DIR', plugin_dir_path( trailingslashit( WP_PLUGIN_DIR ) . 'Ultimate_VC_Addons' . DIRECTORY_SEPARATOR . 'Ultimate_VC_Addons.php' ) );
			}
			if ( ! defined( 'UAVC_URL' ) ) {
				define( 'UAVC_URL', plugins_url( '/', trailingslashit( WP_PLUGIN_DIR ) . 'Ultimate_VC_Addons' . DIRECTORY_SEPARATOR . 'Ultimate_VC_Addons.php' ) );
			}
			$this->vc_template_dir = UAVC_DIR . 'vc_templates' . DIRECTORY_SEPARATOR;
			$this->vc_dest_dir     = get_template_directory() . DIRECTORY_SEPARATOR . 'vc_templates' . DIRECTORY_SEPARATOR;
			$this->module_dir      = UAVC_DIR . 'modules' . DIRECTORY_SEPARATOR;
			$this->params_dir      = UAVC_DIR . 'params' . DIRECTORY_SEPARATOR;
			$this->assets_js       = UAVC_URL . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR;
			$this->assets_css      = UAVC_URL . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR;
			$this->admin_js        = UAVC_URL . 'admin' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR;
			$this->admin_css       = UAVC_URL . 'admin' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR;

			$this->paths          = wp_upload_dir();
			$this->paths['fonts'] = 'smile_fonts';

			$scheme = is_ssl() ? 'https' : 'http';

			$this->paths['fonturl'] = set_url_scheme( $this->paths['baseurl'] . '/' . $this->paths['fonts'], $scheme );
		}
	}
}
