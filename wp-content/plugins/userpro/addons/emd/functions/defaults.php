<?php

	/* get a global option */
	function userpro_ed_get_option( $option ) {
		$userpro_default_options = userpro_ed_default_options();
		$settings = get_option('userpro_ed');
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
	function userpro_ed_set_option($option, $newvalue){
		$settings = get_option('userpro_ed');
		$settings[$option] = $newvalue;
		update_option('userpro_ed', $settings);
	}
	
	/* default options */
	function userpro_ed_default_options(){
		$array['emd_per_page'] = 20;
		$array['emd_layout'] = 'masonry';
		$array['emd_col_width'] = '22%';
		$array['emd_col_margin'] = '2%';
		return apply_filters('userpro_ed_default_options_array', $array);
	}