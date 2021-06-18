<?php
	/* get a global option */

	function userpro_tags_get_option( $option ) {
		$userpro_default_options = userpro_tags_default_options();
		$settings = get_option('userpro');
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
	function userpro_tags_set_option($option, $newvalue){
		$settings = get_option('userpro');
		$settings[$option] = $newvalue;
		update_option('userpro', $settings);
	}
	/* default options */
	function userpro_tags_default_options(){
		
		
		$array['userpro_tags_envato_code'] = '';
		$array['limit_tags'] = '5';
		
		return  $array;
	}
?>
