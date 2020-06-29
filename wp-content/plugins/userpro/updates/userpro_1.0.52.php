<?php

	/* Add the update info on init */
	add_action('init', 'userpro_update_1052', 18);
	function userpro_update_1052(){
	
		if (!userpro_update_installed('1052') && get_option('userpro_fields') && get_option('userpro_fields_builtin') ) {
		        global $userpro;
			$fields = get_option('userpro_fields');
			$builtin = get_option('userpro_fields_builtin');
			
			$new_fields['linkedin'] = array(
			'_builtin' => 1,
			'type' => 'text',
			'label' => 'Linkedin Page'
		);

				
			
			$all_fields = $new_fields+$fields;
			$all_builtin = $new_fields+$builtin;
			
			update_option('userpro_fields', $all_fields);
			update_option('userpro_fields_builtin', $all_builtin);
			update_option("userpro_update_1052", 1);
			$userpro->update_field_icons();
	
		}
	
	}
