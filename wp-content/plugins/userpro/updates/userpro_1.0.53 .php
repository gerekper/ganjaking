<?php

	/* Add the update info on init */
	add_action('init', 'userpro_update_1053', 19);
	function userpro_update_1053(){
	
		if (!userpro_update_installed('1053') && get_option('userpro_fields') && get_option('userpro_fields_builtin') ) {
			global $userpro;
			$fields = get_option('userpro_fields');
			$builtin = get_option('userpro_fields_builtin');
			
			$new_fields['instagram'] = array(
			'_builtin' => 1,
			'type' => 'text',
			'label' => 'Instagram Page'
		);

				
			
			$all_fields = $new_fields+$fields;
			$all_builtin = $new_fields+$builtin;
			
			update_option('userpro_fields', $all_fields);
			update_option('userpro_fields_builtin', $all_builtin);
			update_option("userpro_update_1053", 1);
			$userpro->update_field_icons();
	
		}
	
	}
