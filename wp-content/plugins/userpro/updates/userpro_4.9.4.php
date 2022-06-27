<?php
/* Add the update info on init */
add_action('init', 'userpro_update_494', 20);
function userpro_update_494(){

	if (!userpro_update_installed('494') && get_option('userpro_fields') && get_option('userpro_fields_builtin') ) {
		global $userpro;
			
		$fields = get_option('userpro_fields');
		$builtin = get_option('userpro_fields_builtin');
			
		$new_fields['profile_privacy'] = array(
				'_builtin' => 0,
				'type' => 'checkbox',
				'label' => 'Profile Privacy',
				'options' => array('privacy' => 'Hide the profile completely from everyone'),
				'help' => 'You can check this option to hide the profile completely',
				'hidden' => 1
		);

		
			
		$all_fields = $new_fields+$fields;
		$all_builtin = $new_fields+$builtin;
		
		$userpro_fields_group = get_option('userpro_fields_groups');
		$userpro_fields_group['edit']['default']['user_email']['ajaxcheck'] = 'email_domain_check';
		update_option( 'userpro_fields_groups' , $userpro_fields_group );
			
		update_option('userpro_fields', $all_fields);
		update_option('userpro_fields_builtin', $all_builtin);
		update_option("userpro_update_494", 1);
			

	}

}
