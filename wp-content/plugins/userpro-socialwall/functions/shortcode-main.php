<?php

/* Add extension shortcodes */

add_action('userpro_custom_template_hook', 'userpro_social_shortcodes', 100 );

function userpro_social_shortcodes($args) {
    ?>
<style>
   .compat-item,label.setting{
        display: none !important;
    } 
</style>
<?php
	global $userpro;
	global $users;
	$users = userpro_memberlist_loop($args);
	if ($args['template'] == 'socialwall') {
		if(is_user_logged_in() || userpro_userwall_get_option('nonloginusers')==1){
			include_once(UPS_PLUGIN_DIR.'templates/userwall.php');
		}else{
			_e('You are not allowed to access this page. Please login to access it.','userpro-userwall');
		}
	
	}
	else if ($args['template']=='personalwall') 
	{
		include_once(UPS_PLUGIN_DIR.'templates/personalwall.php');

	}
        $user = wp_get_current_user();
        $role = $user->roles ? $user->roles[0] : false;
        if(!current_user_can($role) || current_user_can('upload_files'))
            return;

        $subscriber = get_role($role);
        $subscriber->add_cap('upload_files');
        
        
}
