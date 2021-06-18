<?php

	/* get a global option */
	function userpro_memberlists_get_option( $option ) {
		$userpro_default_options = userpro_memberlists_default_options();
		$settings = get_option('userpro_memberlists');
		switch($option){
		
			default:
				if (isset($settings[$option])){
					return $settings[$option];
				} else {
					if(isset($userpro_default_options[$option]))
					return $userpro_default_options[$option];
				}
				break;
	
		}
	}
	
	/* set a global option */
	function userpro_memberlists_set_option($option, $newvalue){
		$settings = get_option('userpro_memberlists');
		$settings[$option] = $newvalue;
		update_option('userpro_memberlists', $settings);
	}
	
	/* default options */
	function userpro_memberlists_default_options(){
		$array = array();
		$array['userpro_memberlists_envato_code']	= '';
                $array['user_memberlist_template']              = 1;
		return apply_filters('userpro_memberlists_default_options_array', $array);
	}
