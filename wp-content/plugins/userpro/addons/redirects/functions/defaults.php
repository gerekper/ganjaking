<?php

	/* get a global option */
	function userpro_rd_get_option( $option ) {
		$userpro_default_options = userpro_rd_default_options();
		$settings = get_option('userpro_rd');
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
	function userpro_rd_set_option($option, $newvalue){
		$settings = get_option('userpro_rd');
		$settings[$option] = $newvalue;
		update_option('userpro_rd', $settings);
	}
	
	/* default options */
	function userpro_rd_default_options(){
		$array = array();
		return apply_filters('userpro_rd_default_options_array', $array);
	}