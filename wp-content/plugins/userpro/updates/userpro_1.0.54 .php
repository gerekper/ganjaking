<?php

	/* Add the update info on init */
	add_action('init', 'userpro_update_1054', 19);
	function userpro_update_1054(){
	
		if (!userpro_update_installed('1054') && get_option('userpro_fields') && get_option('userpro_fields_builtin') ) {
			
			$fields = get_option('userpro_fields');
			$builtin = get_option('userpro_fields_builtin');
			
			$new_fields['recaptcha'] = array(
			'_builtin' => 1,
			'sitekey' => '',
			'type' => 'recaptcha',
			'label' => 'reCAPTCHA'
		);
	

				
			
			$all_fields = $new_fields+$fields;
			$all_builtin = $new_fields+$builtin;
			
			update_option('userpro_fields', $all_fields);
			update_option('userpro_fields_builtin', $all_builtin);
			update_option("userpro_update_1054", 1);
			
	
		}
	
	}
