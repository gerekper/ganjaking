<?php

	/* get a global option */
	function userpro_userwall_get_option( $option ) {
		$userpro_default_options = userpro_userwall_default_options();
		$settings = get_option('userpro_userwall');
		switch($option){

			default:
				if (isset($settings[$option])){
					return $settings[$option];
				} else {
					return $userpro_default_options[$option];
				}
				break;

		}
	}

	/* set a global option */
	function userpro_userwall_set_option($option, $newvalue){
		$settings = get_option('userpro_userwall');
		$settings[$option] = $newvalue;
		update_option('userpro_userwall', $settings);
	}

	function userpro_userwall_default_options()
	{
			$array = array();
			$mail_user_on_likedis_post=__('Hi there,') . "\r\n\r\n";
			$mail_user_on_likedis_post=__("{USERPRO_FROM_USERNAME} has {action} your post at {USERPRO_BLOGNAME}","userpro") . "\r\n\r\n";
			$mail_user_on_likedis_comment=__('Hi there,') . "\r\n\r\n";
			$mail_user_on_likedis_comment=__("{USERPRO_FROM_USERNAME} has {action} your comment at {USERPRO_BLOGNAME}","userpro") . "\r\n\r\n";
			$mail_user_on_comment= __('Hi there,') . "\r\n\r\n";
			$mail_user_on_comment.=__("{USERPRO_FROM_USERNAME} has comment on your post at {USERPRO_BLOGNAME}","userpro") . "\r\n\r\n";
			$array['mail_user_on_likedis_post']=$mail_user_on_likedis_post;
			$array['mail_user_on_likedis_post_s']=__('New Like/Dislike on your post','userpro-userwall');
			$array['mail_user_on_likedis_comment']=$mail_user_on_likedis_comment;
			$array['mail_user_on_likedis_comment_s']=__('New Like/Dislike on your post comment','userpro-userwall');
			$array['mail_user_on_comment']=$mail_user_on_comment;
			$array['mail_user_on_comment_s']=__('New comment on your post','userpro-userwall');
			$array['title'] = __('Social Wall','userpro-userwall');
			$array['totalpost'] = __('10','userpro-userwall');
			$array['nonloginusers'] = __('0','userpro-userwall');
			$array['sw_comment_notification'] ='1';
            		$array['userpro_userwall_envato_code'] = '';
			$array['followerspost'] = '0';
			$array['display_socialbutton'] = '0';
			$array['allow_mediabutton'] = '0';	
			$array['limit_number_of_post']='-1';
			$array['limit_number_of_comment']='12';
			$array['userpro-userwall_roles_can_poston_wall']='';
			$array['postcontent_color'] = '#000000';
			$array['send_email_on_comment']	= 0;
			$array['send_email_on_post_likedis']= 0;
			$array['send_email_on_comment_likedis']	= 0;
			$array['enablepersonalwall'] = 0;
			$array['personalwall_title'] = 'Personal Wall';


			return apply_filters('userpro_userwall_default_options_array', $array);
	}
