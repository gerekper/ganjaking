<?php

if( !class_exists( 'UPGMap' ) ){
	
	class UPGMap{
		
		function __construct(){

			$this->define_constants();
			
			global $userpro;
			/* Priority actions */
			$this->include_files();
			if(userpro_gmap_get_option('enable_gmap')){
				add_action('wp_enqueue_scripts', array($this,'load_assets') , 9);
				$this->userpro_update_address();
			}
		}
		/* Define path and url contants */

		/* Add the update info on init */
		function userpro_update_address(){
			if (!userpro_update_installed('address') && get_option('userpro_fields') && get_option('userpro_fields_builtin') ) {
				$fields = get_option('userpro_fields');
				$builtin = get_option('userpro_fields_builtin');
			
				$new_fields['address_line_1'] = array(
					'_builtin' => 1,
					'type' => 'text',
					'label' => 'Address Line 1'
				);

				$new_fields['address_line_2'] = array(
					'_builtin' => 1,
					'type' => 'text',
					'label' => 'Address Line 2'
				);

				$new_fields['address_line_3'] = array(
					'_builtin' => 3,
					'type' => 'text',
					'label' => 'Address Line 3'
				);

			
				$all_fields = array_merge($new_fields, $fields);
				$all_builtin = array_merge($new_fields, $builtin);
			
				update_option('userpro_fields', $all_fields);
				update_option('userpro_fields_builtin', $all_builtin);
				update_option("userpro_update_address", 1);		
			}	
		}
		
		function define_constants(){			
			define( 'UPGMAP_PATH', plugin_dir_path( __FILE__ ) );
			define( 'UPGMAP_URL', plugin_dir_url( __FILE__ ) );
		}
		
		/* Includes files from includes/classes folder and also includes admin.php */		
		function include_files(){
			require_once(UPGMAP_PATH.'/functions/defaults.php');	
			require_once (UPGMAP_PATH.'functions/shortcode-main.php');
			if( is_admin() ){
				include_once( UPGMAP_PATH. 'admin/admin.php' );
			}else{
				include_once(UPGMAP_PATH.'functions/class-upgmap-ajax.php');
			}
		}

		public function load_assets(){
			wp_enqueue_script('jquery');
                        $key = userpro_gmap_get_option('userpro_gmap_key');
			if( userpro_gmap_get_option('enable_gmap') && !empty($key)) {
                wp_enqueue_script('up_google_map_script', 'https://maps.googleapis.com/maps/api/js?key=' . $key);
                wp_register_script('gmap_js', UPGMAP_URL . 'assets/js/gmap.js', array('up_google_map_script'), '', true);
                $gkey_array = array(
                    'gmap_key_value' => $key
                );
                wp_localize_script('gmap_js', 'gmap_object', $gkey_array);
            }
		// Enqueued script with localized data.
			wp_enqueue_script( 'gmap_js');
		}		
	}
	new UPGMap();
}
