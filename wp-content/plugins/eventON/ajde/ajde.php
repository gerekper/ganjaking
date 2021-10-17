<?php
/**
 * AJDE Plugin Settings Library
 * @version 	1.6.0
 * @updated 	2020
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class ajde{

	public $version = '1.7.0';

	public function __construct(){

		$this->path = plugin_dir_url( __FILE__ );

		// text domain for language translations
		$this->domain = 'ajde';

		if(is_admin()){
			include_once('ajde-wp-admin.php');
			$this->wp_admin = new ajde_wp_admin();

			add_action('admin_enqueue_scripts', array($this, 'load_styles_scripts' ));
		}
	}

	// load styles and scripts for all wp-admin pages
		function load_styles_scripts(){
			if(!is_admin()) return;		

			$this->register_backender_scripts();
			$this->register_backender_styles();
			$this->wp_admin_styles();
		}

	// wp-admin styles and scripts
		public function wp_admin_styles(){
			EVO()->elements->enqueue_shortcode_generator();
		}

	// register scripts
		function register_scripts(){
			$this->register_backender_styles();
			$this->register_backender_scripts();
		}

	// backender
		public function load_ajde_backender(){
			include_once(  AJDE_EVCAL_PATH.'/includes/admin/settings/class-settings.php' );
			$this->settings = new EVO_Settings();			
		}
		// can be called from within pages
			public function load_styles_to_page(){}
			function load_scripts_to_page(){}

		// registering
			public function register_backender_styles(){	
			}

			public function register_backender_scripts(){
				EVO()->elements->register_shortcode_generator_styles_scripts();
			}

		// Other
		function load_colorpicker(){
			EVO()->elements->load_colorpicker();
		}
		function register_colorpicker(){
			EVO()->elements->register_colorpicker();
		}

}
