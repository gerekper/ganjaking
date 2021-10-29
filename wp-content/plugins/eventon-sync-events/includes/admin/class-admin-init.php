<?php
/**
 * Admin class for sync events plugin
 *
 * @version 	0.1
 * @author  	AJDE
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evosy_admin{
	function __construct(){			
		
		// settings
		add_action( 'admin_menu', array( $this, 'menu' ),9);	
		
		// print settings styles
		if(!empty($_GET['page']) && $_GET['page']=='evosy'){			
			add_action( 'admin_init', array($this, 'admin_styles'));
		}
	}

	// eventon settings only styles
		function admin_styles(){

			global $ajde, $eventon_sy;

			// load backender
			$ajde->load_ajde_backender();

			wp_enqueue_style( 'evosy_admin',$eventon_sy->plugin_url.'/assets/admin.css', array(), $eventon_sy->version);
			wp_enqueue_script( 'evosy_script',$eventon_sy->plugin_url.'/assets/admin.js',array('jquery'), $eventon_sy->version, true );
			wp_localize_script( 
				'evosy_script', 
				'evosy_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'eventonsy_nonce' )
				)
			);
		}

	// MENUS
		function menu(){
			add_submenu_page( 'eventon', __('Sync Events','eventon'), 'Sync Events', 'manage_eventon', 'evosy', array($this,'page_content') );
		}
		function page_content(){
			global $eventon;				

			if(version_compare(phpversion(), '5.4','<')){
				echo "<p style='padding:30px 0;'>".__('You need PHP version 5.4 or higher for this EventON Sync to work properly.','eventon')."</p>";
			}else{
				require_once('class-settings.php');
			}				
		}
}