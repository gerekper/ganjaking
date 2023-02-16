<?php
/**
 * Bookings Frontend
 * @version 0.1
 */

class evobo_frontend{
	public function __construct(){
		
		add_action( 'evo_register_other_styles_scripts', array( $this, 'register_styles_scripts' ) ,15);
		add_action( 'wp_enqueue_scripts', array( $this, 'load_styles' ), 10 );
	}
// styles and scripts
	function register_styles_scripts(){
		wp_register_style( 'evobo_styles',EVOBO()->assets_path.'evobo_styles.css', array(), EVOBO()->version);
		wp_register_script('evobo_script', EVOBO()->assets_path.'evobo_script.js', array('jquery'), EVOBO()->version, true);
		wp_localize_script(
			'evobo_script',
			'evobo_ajax_obj',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ) ,
				'postnonce' => wp_create_nonce( 'evobo_nonce' )
			)
		);
	}
	function load_styles(){
		wp_enqueue_script('evobo_script');
		wp_enqueue_style('evobo_styles');
	}
}