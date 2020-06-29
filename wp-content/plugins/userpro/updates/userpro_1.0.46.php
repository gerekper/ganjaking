<?php

	/* Add the update info on init */
	add_action('init', 'userpro_update_1046', 15);
	function userpro_update_1046(){
	
		if (!userpro_update_installed('1046') && get_option('userpro_fields') && get_option('userpro_fields_builtin') ) {
		
			$fields = get_option('userpro_fields');
			$builtin = get_option('userpro_fields_builtin');
			
			$new_fields['envato_purchase_code'] = array(
				'_builtin' => 1,
				'type' => 'text',
				'label' => 'Envato Purchase Code',
				'help' => 'Please enter your envato purchase code.',
				'hidden' => 1
			);
			
			$all_fields = $new_fields+$fields;
			$all_builtin = $new_fields+$builtin;
			
			update_option('userpro_fields', $all_fields);
			update_option('userpro_fields_builtin', $all_builtin);
			update_option("userpro_update_1046", 1);
		
		}
	
	}