<?php

	/* Add the update info on init */
	add_action('init', 'userpro_update_1055', 20);
	function userpro_update_1055(){
	
		if (!userpro_update_installed('1055') && get_option('userpro_fields') && get_option('userpro_fields_builtin') ) {
			global $userpro;
			
			$fields = get_option('userpro_fields');
			$builtin = get_option('userpro_fields_builtin');
			
			$new_fields['youtube'] = array(
			'_builtin' => 1,
			'type' => 'text',
			'label' => 'Youtube Url',
			'icon' =>'youtube'
		);

				
			
			$all_fields = $new_fields+$fields;
			$all_builtin = $new_fields+$builtin;
			
			update_option('userpro_fields', $all_fields);
			update_option('userpro_fields_builtin', $all_builtin);
			update_option("userpro_update_1055", 1);
			
	
		}
	
	}
