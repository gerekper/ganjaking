<?php
/**
 * Frontend
 * @version 0.1
 */

class evovo_frontend{
	public function __construct(){

		add_action( 'init', array( $this, 'register_styles_scripts' ) ,15);
		add_action( 'wp_enqueue_scripts', array( $this, 'load_styles' ), 10 );
		
		$this->opt2 = get_option('evcal_options_evcal_2');		

	}
	// styles and scripts
		function register_styles_scripts(){
			wp_register_style( 'evovo_styles',EVOVO()->assets_path.'evovo_styles.css', array(), EVOVO()->version);
			wp_register_script('evovo_script', EVOVO()->assets_path.'evovo_script.js', array('jquery'), EVOVO()->version, true);
			wp_localize_script(
				'evovo_script',
				'evovo_ajax_obj',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ) ,
					'postnonce' => wp_create_nonce( 'evovo_nonce' )
				)
			);
		}
		function load_styles(){
			wp_enqueue_script('evovo_script');
			wp_enqueue_style('evovo_styles');
		}

	// get language fast for evo_lang
		function lang($text){	return evo_lang($text, '', $this->opt2);}
		function langE($text){ echo $this->lang($text); }
		function langX($text, $var){	return eventon_get_custom_language($this->opt2, $var, $text);	}
		function langEX($text, $var){	echo eventon_get_custom_language($this->opt2, $var, $text);		}
}