<?php


if( !class_exists( 'upml_layout_switcher' ) ){
	
	class upml_layout_switcher{
		
		function __construct(){
                    
		}
		
		function upml_load_layout($template_number ){
			global $userpro_memberlists;
                        global $userpro;
			//$profile_thumb_size = $args['profile_thumb_size'];
			/* Dequeue userpro default style before adding new */
			wp_dequeue_style('userpro_skin_min-css');
			
			foreach (glob(userpro_memberlists_path.'templates/template'.$template_number.'/css/*.css') as $filename) { 
				$filename = basename($filename);
				wp_enqueue_style( "upml_layout_style_$filename", userpro_memberlists_url.'templates/template'.$template_number.'/css/'.$filename );
			}
			
			foreach (glob(userpro_memberlists_path.'templates/template'.$template_number.'/js/*.js') as $filename) {
				$filename = basename($filename);
				wp_enqueue_script("upml_layout_script_$filename",userpro_memberlists_url.'templates/template'.$template_number.'/js/'.$filename,'','',true);
			}
		}
		
	}
}

