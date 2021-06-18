<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( !class_exists( 'UPDBHookActions' ) ){

	class UPDBHookActions{
		
		function __construct(){
			
			add_action( 'after_userpro_profile_div', array( $this, 'updb_show_widgets' ), 10, 3 );
			if( !isset( $updb_default_options ) ){
				$updb_default_options = new UPDBDefaultOptions();
			}
			if($updb_default_options->updb_get_option('custom_widget_section') == 1){
				add_filter('updb_default_options_array',array($this,'custom_widgets_in_dashboard'),'10','1');
			}
		}

		function updb_show_widgets( $args, $user_id, $i ){
			$template = $args['template'];
			include UPDB_PATH.'templates/customizer/widget-container.php';
		}
		
		function custom_widgets_in_dashboard($array){
			$updb_custom_widgets = get_option('updb_custom_widgets');
			if(!empty($updb_custom_widgets)){
				foreach($updb_custom_widgets as $k => $v){
					$olddata=$array['updb_available_widgets'];
					$newdata= array ($k =>array('title'=>$v['title'], 'widget_content'=>$v['content'] ));
					$array['updb_available_widgets']=   array_merge($olddata,$newdata);
					$oldunsetwidgets=$array['updb_unused_widgets'];
					$newunsetwidgets= array($k);
					$array['updb_unused_widgets']= array_merge($oldunsetwidgets,$newunsetwidgets);
				}
				return $array;
			}
                        else{
                            return $array;
                        }
		}

	}
	new UPDBHookActions();
}