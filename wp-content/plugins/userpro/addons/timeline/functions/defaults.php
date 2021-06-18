<?php

	/* get a global option */
	function userpro_timeline_get_option( $option ) {
		$userpro_default_options = userpro_timeline_default_options();
		$settings = get_option('userpro_timeline');
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
	function userpro_timeline_set_option($option, $newvalue){
		$settings = get_option('userpro_timeline');
		$settings[$option] = $newvalue;
		update_option('userpro_timeline', $settings);
	}

	/* default options */
	function userpro_timeline_default_options(){

		$array['enable_timeline'] = 0;

		return apply_filters('userpro_timeline_default_options_array', $array);
	}
