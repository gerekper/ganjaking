<?php
/**
 * Event seats front end class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON-st/classes
 * @version     0.1
 */
class evost_front{
	
	function __construct(){
		global $evost;

		$this->evopt1 = get_option('evcal_options_evcal_1');
		$this->evopt2 = get_option('evcal_options_evcal_2');
		$this->opt_tx = get_option('evcal_options_evcal_tx');

		// scripts and styles 
		add_action( 'init', array( $this, 'register_styles_scripts' ) ,15);	

		
	}

	// STYLES:  
		public function register_styles_scripts(){
			if(is_admin()) return;
			
			wp_register_style( 'evost_styles',EVOST()->assets_path.'ST_styles.css');
			
			wp_register_script('evost_draw',EVOST()->assets_path.'evost_map_draw.js', array('jquery'), EVOST()->version, true );
			wp_register_script( 'evost_handlebars',EVOST()->assets_path.'handlebars.js',array('jquery'), EVOST()->version, true);
			wp_register_script('evost_script',EVOST()->assets_path.'ST_script.js', array('jquery'), EVOST()->version, true );
			wp_localize_script( 
				'evost_script', 
				'evost_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'evost_nonce' )
				)
			);
			
			$this->print_scripts();
			add_action( 'wp_enqueue_scripts', array($this,'print_styles' ));				
		}
		public function print_scripts(){				
			//wp_enqueue_script('evost_handlebars');			
			wp_enqueue_script('evost_draw');			
			wp_enqueue_script('evost_script');		
		}
		function print_styles(){	wp_enqueue_style( 'evost_styles');	}

	// SUPPORT functions
		// RETURN: language
			function lang($variable, $default_text){
				global $evost;
				return $evost->lang($variable, $default_text);
			}		
}