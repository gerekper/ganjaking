<?php

	/* get a global option */
	function userpro_gmap_get_option( $option ) {
		$userpro_default_options = userpro_gmap_default_options();
		$settings = get_option('userpro_gmap');
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
	function userpro_gmap_set_option($option, $newvalue){
		$settings = get_option('userpro_gmap');
		$settings[$option] = $newvalue;
		update_option('userpro_gmap', $settings);
	}
	
	/* default options */
	function userpro_gmap_default_options(){
		$body = __('Hello,','userpro-gmap') . "\r\n\r\n";
		$body .= __('Thanks and Regards,') . "\r\n\r\n";
		$body .= __('<a href="{USERPRO_REPLY_URL}">Click here</a>');

		$array['userpro_gmap_envato_code']	= '';
		$array['enable_gmap'] = 0;
		$array['userpro_gmap_key'] = '';

		$array['contact_mail_s'] = __('Received Mail via Contact Me');		
		$array['contact_mail_c'] = $body;

		if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();

		return apply_filters('userpro_gmap_default_options_array', $array);
	}
