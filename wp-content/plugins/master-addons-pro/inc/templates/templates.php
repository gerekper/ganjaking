<?php
	/**
	 * Author Name: Liton Arefin
	 * Author URL: https://jeweltheme.com
	 * Date: 9/8/19
	 */

	namespace MasterAddons\Inc\Templates;

	use MasterAddons\Inc\Templates\Types;

	if ( ! defined('ABSPATH') ) exit;


	if ( ! class_exists('Master_Templates') ) {


		class Master_Templates {

			private static $instance = null;
			public $api;
			public $config;
			public $assets;
			public $temp_manager;
			public $types;

			public function __construct() {
				add_action( 'init', array( $this, 'init' ) );
			}

			public function init() {

				$this->load_files();

				$this->set_config();

				$this->set_assets();

				$this->set_api();

				$this->set_types();

				$this->set_templates_manager();

			}

			private function load_files() {

				require MELA_PLUGIN_PATH . '/inc/templates/classes/config.php';

				require MELA_PLUGIN_PATH . '/inc/templates/classes/assets.php';

				require MELA_PLUGIN_PATH . '/inc/templates/classes/manager.php';

				require MELA_PLUGIN_PATH . '/inc/templates/types/manager.php';

				require MELA_PLUGIN_PATH . '/inc/templates/classes/api.php';
			}


			private function set_config() {

				$this->config       = new Classes\Master_Addons_Templates_Core_Config();

			}


			private function set_assets() {

				$this->assets       = new Classes\Master_Addons_Templates_Assets();

			}


			private function set_api() {

				$this->api       = new Classes\Master_Addons_Templates_API();

			}


			private function set_types() {

				$this->types        = new Types\Master_Addons_Templates_Types();

			}


			private function set_templates_manager() {

				$this->temp_manager = new Classes\Master_Addons_Templates_Manager();

			}


			public static function get_instance() {
				if( self::$instance == null ) {
					self::$instance = new self;
				}
				return self::$instance;
			}

		}

	}

	if ( ! function_exists('master_addons_templates') ) {
		function master_addons_templates() {
			return Master_Templates::get_instance();
		}
	}
	master_addons_templates();