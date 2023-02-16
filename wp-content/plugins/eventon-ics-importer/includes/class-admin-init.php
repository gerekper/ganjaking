<?php
/**
 * Admin class for ICS importer plugin
 *
 * @version 	0.2
 * @author  	AJDE
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EVOICS_admin{
	var $log= array();
	public $evo_opt;

	function __construct(){
		add_action('admin_init', array($this, 'admin_scripts'));
		
		// settings link in plugins page
		add_filter("plugin_action_links_". EVOICS()->plugin_slug, array($this,'eventon_plugin_links' ));
		add_action( 'admin_menu', array( $this, 'menu' ),9);

		$evo_opt = get_option('evcal_options_evcal_1');
	}
	/**	Add the tab to settings page on myeventon	 */
		function tab_array($evcal_tabs){
			$evcal_tabs['evcal_ics']='ICS Import';
			return $evcal_tabs;
		}
	// EventON settings menu inclusion
		function menu(){
			add_submenu_page( 'eventon', 'ICS Import', __('ICS Import','eventon'), 'manage_eventon', 'evoics', array($this, 'page_content') );
		}

	/**	ICS settings content	 */
		function page_content(){
			require_once('class-settings.php');
			$SET = new EVOICS_settings();
			$SET->content();

			include_once(  AJDE_EVCAL_PATH.'/includes/admin/settings/class-settings.php' );
			$this->settings = new EVO_Settings();
			$this->settings->register_ss();
			$this->settings->load_styles_scripts();
		}

	// Styles and scripts for the page
		public function admin_scripts(){
			global $evoics, $pagenow, $eventon, $ajde;

			if( (!empty($pagenow) && $pagenow=='admin.php')
			 && (!empty($_GET['page']) && $_GET['page']=='evoics') 
			){
				// LOAD ajde library
				if(isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'evoics_1'){
					$ajde->load_ajde_backender();
				}

				wp_enqueue_style( 'ics_import_styles',$evoics->assets_path.'ics_import_styles.css');
				wp_enqueue_script('ics_import_script',$evoics->assets_path.'script.js', array('jquery'), 1.0, true );
				wp_localize_script( 
					'ics_import_script', 
					'evoics_ajax_script', 
					array( 
						'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
						'postnonce' => wp_create_nonce( 'evoics_nonce' )
					)
				);
			}
		}
	
	// SECONDARY FUNCTIONS
    	function eventon_plugin_links($links){
			$settings_link = '<a href="admin.php?page=evoics">Settings</a>'; 
			array_unshift($links, $settings_link); 
	 		return $links; 	
		}
}
