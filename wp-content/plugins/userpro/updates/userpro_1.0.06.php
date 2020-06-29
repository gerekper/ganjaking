<?php

	/* Add the update info on init */
	add_action('init', 'userpro_update_1006', 12);
	function userpro_update_1006(){
	
		if (!userpro_update_installed('1006') && get_option('userpro_fields') && get_option('userpro_fields_builtin') ) {
		
			$fields = get_option('userpro_fields');
			$builtin = get_option('userpro_fields_builtin');
			
			$new_fields['phone_number'] = array(
				'_builtin' => 1,
				'type' => 'text',
				'label' => 'Phone Number'
			);
			
			$all_fields = $new_fields+$fields;
			$all_builtin = $new_fields+$builtin;
			
			update_option('userpro_fields', $all_fields);
			update_option('userpro_fields_builtin', $all_builtin);
			update_option("userpro_update_1006", 1);
		
		}
	
	}