<?php

	/* get a global option */
	function userpro_sc_get_option( $option ) {
		$userpro_default_options = userpro_sc_default_options();
		$settings = get_option('userpro_sc');
		switch($option){
		
			default:
				if (isset($settings[$option])){
					return $settings[$option];
				} else {
					if (isset(  $userpro_default_options[$option] ) ) {
					return $userpro_default_options[$option];
					}
				}
				break;
	
		}
	}
	
	/* set a global option */
	function userpro_sc_set_option($option, $newvalue){
		$settings = get_option('userpro_sc');
		$settings[$option] = $newvalue;
		update_option('userpro_sc', $settings);
	}
	
	/* default options */
	function userpro_sc_default_options(){
	
		$mail_new_follow = __('Hi there,','userpro') . "\r\n\r\n";
		$mail_new_follow .= __("{USERPRO_FOLLOWER_NAME} is now following you on {USERPRO_BLOG_NAME}! You can click the following link to view his/her profile:","userpro") . "\r\n";
		$mail_new_follow .= "{USERPRO_FOLLOWER_LINK}" . "\r\n\r\n";
		$mail_new_follow .= __("Or view your profile at:","userpro") . "\r\n";
		$mail_new_follow .= "{USERPRO_MY_PROFILE}" . "\r\n\r\n";
		$mail_new_follow .= __('This is an automated notification that was sent to you by UserPro. No further action is needed.','userpro');
		
		$array['mail_new_follow_s'] = __('{USERPRO_FOLLOWER_NAME} is now following you!','userpro');
		$array['mail_new_follow'] = $mail_new_follow;
		
		$mail_new_post_follow_m = __('Hi there,','userpro') . "\r\n\r\n";
		$mail_new_post_follow_m .= __("{USERPRO_FROM_NAME} has created a new post - ","userpro") . "\r\n";
		$mail_new_post_follow_m .= __("Post Name : {VAR1}","userpro");
		
		$array['mail_new_post_follow_s'] = __('{USERPRO_FROM_NAME} has created a new post!','userpro');
		$array['mail_new_post_follow_m'] = $mail_new_post_follow_m;
		
		
		$array['slug_following'] = 'following';
		$array['slug_followers'] = 'followers';
		$array['activity_open_to_all'] = 1;
		$array['activity_per_page'] = 10;
		$array['excluded_post_types'] = 'nav_menu_item';
		$array['notification_on_follow'] = 1;
		$array['notification_on_follow_post'] = 0;
		$array['hide_admins'] = 0;
		return apply_filters('userpro_sc_default_options_array', $array);
	}