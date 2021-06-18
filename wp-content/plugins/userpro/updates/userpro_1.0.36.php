<?php

	/* Add the update info on init */
	add_action('init', 'userpro_update_1036', 14);
	function userpro_update_1036(){
	
		if (!userpro_update_installed('1036') && get_option('userpro_fields') && get_option('userpro_fields_builtin') ) {
		
			$fields = get_option('userpro_fields');
			$builtin = get_option('userpro_fields_builtin');
			
			$new_fields['custom_profile_bg'] = array(
				'_builtin' => 1,
				'type' => 'picture',
				'label' => 'Profile Background',
				'help' => 'Add a custom profile background to your profile'
			);
			
			$new_fields['custom_profile_color'] = array(
				'_builtin' => 1,
				'type' => 'select',
				'label' => 'Heading Color',
				'options' => array('' => 'Choose Skin', 'light' => 'Light', 'dark' => 'Dark'),
				'default' => 'Dark',
				'help' => 'You can choose the style that matches your custom background (if you set one)'
			);
			
			$all_fields = $new_fields+$fields;
			$all_builtin = $new_fields+$builtin;
			
			update_option('userpro_fields', $all_fields);
			update_option('userpro_fields_builtin', $all_builtin);
			update_option("userpro_update_1036", 1);
		
		}
	
	}