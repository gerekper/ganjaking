<?php

	/* Add the update info on init */
	add_action('init', 'userpro_update_1048', 16);
	function userpro_update_1048(){
	
		if (!userpro_update_installed('1048') && get_option('userpro_fields') && get_option('userpro_fields_builtin') ) {
		
			$fields = get_option('userpro_fields');
			$builtin = get_option('userpro_fields_builtin');
			
			$new_fields['mailchimp_subscribe'] = array(
				'_builtin' => 1,
				'type' => 'mailchimp',
				'label' => 'Subscribe to newsletter',
				'list_id' => '',
				'list_text' => 'Get periodic e-mail updates and newsletters'
			);
			
			$all_fields = $new_fields+$fields;
			$all_builtin = $new_fields+$builtin;
			
			update_option('userpro_fields', $all_fields);
			update_option('userpro_fields_builtin', $all_builtin);
			update_option("userpro_update_1048", 1);
		
		}
	
	}
