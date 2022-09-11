<?php
/**
 * Wishlist Manager
 * @version 1.0
 */

class EVOWI_Wishlist_Manager{

	function __construct(){
		add_shortcode('add_eventon_wishlist_manager', array($this,'wishlist_manager_content'));
	}

	// wishlist manager
		public function wishlist_manager_content($atts){
						
			if(!$atts || empty($atts)) $atts = array();

			
			return $this->wishlist_template_load($atts);
		}

		function wishlist_template_load($atts){
			
			
			if(empty($atts)) $atts = array();
			
			// set global language
				$this->lang = (!empty($atts['lang']))? $atts['lang']:'L1';
				evo_set_global_lang($this->lang);	

			$this->wishlist_manager = true;
			EVOWI()->front->wishlist_manager = true;
						
			// loading child templates
				$file_name = 'wishlist-manager.php';
				$paths = array(
					0=> TEMPLATEPATH.'/'. EVO()->template_url.'wishlist/',
					1=> STYLESHEETPATH.'/'. EVO()->template_url.'wishlist/',
					2=> EVOWI()->plugin_path.'/templates/',
				);

				foreach($paths as $path){	
					if(file_exists($path.$file_name) ){	
						$template = $path.$file_name;	
						break;
					}
				}

			
			ob_start();
			include($template);
			
			return ob_get_clean();			
			
		}
}
