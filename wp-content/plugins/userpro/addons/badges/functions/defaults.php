<?php

	/* get a global option */
	function userpro_dg_get_option( $option ) {
		$userpro_default_options = userpro_dg_default_options();
		$settings = get_option('userpro_dg');
		switch($option){
		
			default:
				if (isset($settings[$option])){
					return $settings[$option];
				} else {
					return $userpro_default_options[$option];
				}
				break;
	
		}
	}

	/* set a global option */
	function userpro_dg_set_option($option, $newvalue){
		$settings = get_option('userpro_dg');
		$settings[$option] = $newvalue;
		update_option('userpro_dg', $settings);
	}
	
	/* default options */
	function userpro_dg_default_options(){
		$array = array();
		return apply_filters('userpro_dg_default_options_array', $array);
	}