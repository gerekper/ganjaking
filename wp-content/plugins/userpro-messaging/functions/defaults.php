<?php

	/* get a global option */
	function userpro_msg_get_option( $option ) {
		$userpro_default_options = userpro_msg_default_options();
		$settings = get_option('userpro_msg');
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
	function userpro_msg_set_option($option, $newvalue){
		$settings = get_option('userpro_msg');
		$settings[$option] = $newvalue;
		update_option('userpro_msg', $settings);
	}
	
	/* default options */
	function userpro_msg_default_options(){
		$mail_new_msg  = __('Hi there,','userpro-msg') . "\r\n\r\n";
		$mail_new_msg .= __('You have received a new message on {USERPRO_BLOGNAME} from {USERPRO_FROM_NAME}','userpro-msg') . "\r\n\r\n";
		$mail_new_msg .= __('Here is the message body:','userpro-msg') . "\r\n\r\n";
		$mail_new_msg .= "==========================================="  . "\r\n\r\n";
		$mail_new_msg .= "{USERPRO_MESSAGE}" . "\r\n\r\n";
		$mail_new_msg .= __('To reply to this conversation or read more messages, please view your messages by logging to your profile:','userpro-msg') . "\r\n\r\n";
		$mail_new_msg .= "{USERPRO_TO_PROFILE_LINK}";
		$mail_new_msg_s = __('{USERPRO_FROM_NAME} has messaged you!','userpro-msg');
		
		$mail_broadcast_msg  = __('Hi there,','userpro-msg') . "\r\n\r\n";
		$mail_broadcast_msg .= __('Your message has been broadcasted successfully','userpro-msg') . "\r\n\r\n";
		$mail_broadcast_msg .= __('The message you have broadcasted is :','userpro-msg') . "\r\n\r\n";
		$mail_broadcast_msg .= "===========================================" . "\r\n\r\n";
		$mail_broadcast_msg .= "{USERPRO_MESSAGE}" . "\r\n\r\n";
		$mail_broadcast_msg .= "===========================================";
		$mail_broadcast_msg_s = __('Your Message has been Broadcast Successful!','userpro-msg');
		
		$array = array();
		$array['roles_that_can_recive_message'] = '';
		$array['roles_that_can_send_message'] = '';
		$array['msg_conversation'] = '1';
		$array['msg_privacy'] = 'public';
		$array['enterforsend'] = '1';
		$array['show_send_message'] = '1';
		$array['allow_html_content'] = '1';
		$array['send_new_message_mail_user'] = '1';
		$array['broadcast_followers'] = '0';
		$array['msg_notification'] = 'r';
		$array['msg_auto_welcome'] = 0;
		$array['default_msg'] = '0';
		$array['autorefresh'] = 0;
		$array['block_user'] = 0;
		$array['following_user'] = 0;
		$array['user_followers'] = 0;
		$array['default_msg_text'] = 'This website is not responsible for any message content sent by any user. It does not reflect our views / opinions.';
		$array['msg_auto_welcome_id'] = 1;
		$array['msg_auto_welcome_text'] = 'Welcome to UserPro! This is an automatic welcome message sent to you using the private messaging add-on. I am happy to have you as a member, If you are interested about the private message add-on you can view more information here: http://bit.ly/1icXlMN Thank you! :)';
		$array['email_notifications'] = 0;
		$array['broadcast_enabled']=0;
		$array['allow_msg_connections']=0;
		$array['roles_that_can_broadcast']="";
		$array['roles_that_can_send']=get_option('roles_that_can_send');
		$array['roles_that_can_recieve_broadcast'] = "";
		$array['userpro_msg_envato_code'] = "";
		$array['mail_new_msg'] = $mail_new_msg;
		$array['mail_new_msg_s'] = $mail_new_msg_s;
		$array['mail_broadcast_msg'] = $mail_broadcast_msg;
		$array['mail_broadcast_msg_s'] = $mail_broadcast_msg_s;
                $array['roles_that_can_send_message_for_connections'] = '';
		return apply_filters('userpro_msg_default_options_array', $array);
	}
