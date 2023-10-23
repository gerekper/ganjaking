<?php

	/* get a global option */
	function userpro_mu_get_option( $option ) {
		$userpro_default_options = userpro_mu_default_options();
		$settings = get_option('userpro_mu');
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
	function userpro_mu_set_option($option, $newvalue){
		$settings = get_option('userpro_mu');
		$settings[$option] = $newvalue;
		update_option('userpro_mu', $settings);
	}
	
	/* default options */
	function userpro_mu_default_options(){
		$array = array();
		$array['multi_forms'] = '';
		$array['multi_forms_default'] = '';
		return apply_filters('userpro_mu_default_options_array', $array);
	}