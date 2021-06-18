<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( !class_exists( 'UPDBDefaultOptions' ) ){

class UPDBDefaultOptions{

	function __contstruct(){
		
	}

	/* get a global option */
	function updb_get_option( $option ) {
		$updb_default_options = $this->updb_default_options();
		$settings = get_option('userpro_db');
		switch($option){
		
			default:
				if (isset($settings[$option])){
					return $settings[$option];
				} else {
					return $updb_default_options[$option];
				}
				break;
	
		}
	}
	
	/* set a global option */
	function updb_set_option($option, $newvalue){
		$settings = get_option('userpro_db');
		$settings[$option] = $newvalue;
		update_option('userpro_db', $settings);
	}

	function updb_default_options(){
		$template_path = UPDB_PATH.'templates/customizer/';
		/* Order Tab Default options */
		$array = array();
		$array['slug_dashboard'] = 'dashboard';
		$array['userpro_db_enable'] = 1;
		$array['userpro_db_custom_layout'] = 0;
		$array['show_profile_customizer'] = 1;
		$array['custom_widget_section'] = 0;
		$array['updb_admin_widget_layout_1'] = $array['updb_admin_widget_layout_2'] = $array['updb_admin_widget_layout_3'] = "";
		$array['updb_unused_widgets_admin'] = "";
		$array['number_of_column'] = 3;
		$array['updb_available_widgets'] = array( 'profile_details'=>array('title'=>'Profile details', 'template_path'=>$template_path),
							  'followers'=>array('title'=>'My followers', 'template_path'=>$template_path),
							  'following'=>array('title'=>'People I follow', 'template_path'=>$template_path),
							  'activity'=>array('title'=>'My activity', 'template_path'=>$template_path ),
							  'postsbyuser'=>array('title'=>'Posts By User', 'template_path'=>$template_path));
		$array['updb_unused_widgets'] = array( 'profile_details', 'followers', 'following', 'activity', 'postsbyuser' );
                $array['userpro_dashboard_code'] = '';
                $array['userpro_db_post_enable'] = 1;
                $array['userpro_db_post_count'] = 5;
		return apply_filters('updb_default_options_array', $array);
	}
}

	new UPDBDefaultOptions();
}
