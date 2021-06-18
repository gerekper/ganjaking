<?php
/**
 Sends mail
 This function manage the Mail stuff sent by plugin
 to users
 **/
function userpro_mail_set_content_type( $content_type ) {
	return 'text/html';
}

function userpro_mail( $id, $template = null, $var1 = null, $form = null, $from_user = null ) {
	global $userpro;
	add_filter( 'wp_mail_content_type', 'userpro_mail_set_content_type' );
	if ( $template == 'userpro_connect_request' ) {
		$user = get_userdata( get_current_user_id() );
	} else {
		$user = get_userdata( $id );
	}
	$builtin = array( 
		'{USERPRO_ADMIN_EMAIL}' => userpro_get_option( 'mail_from' ), 
		'{USERPRO_BLOGNAME}' => userpro_get_option( 'mail_from_name' ), 
		'{USERPRO_BLOG_URL}' => home_url(), 
		'{USERPRO_BLOG_ADMIN}' => admin_url(), 
		'{USERPRO_LOGIN_URL}' => $userpro->permalink( 0, 'login' ), 
		'{USERPRO_USERNAME}' => $user->user_login, 
		'{USERPRO_FIRST_NAME}' => userpro_profile_data( 'first_name', $user->ID ), 
		'{USERPRO_LAST_NAME}' => userpro_profile_data( 'last_name', $user->ID ), 
		'{USERPRO_NAME}' => userpro_profile_data( 'display_name', $user->ID ), 
		'{USERPRO_EMAIL}' => $user->user_email, 
		'{USERPRO_PROFILE_LINK}' => $userpro->permalink( $user->ID ), 
		'{USERPRO_VALIDATE_URL}' => $userpro->create_validate_url( $user->ID ), 
		'{USERPRO_PENDING_REQUESTS_URL}' => admin_url() . '?page=userpro&tab=requests', 
		'{USERPRO_ACCEPT_VERIFY_INVITE}' => $userpro->accept_invite_to_verify( $user->ID ) );

	if ( isset( $var1 ) && ! empty( $var1 ) ) {
		$builtin['{VAR1}'] = $var1;
	}
	
	if ( isset( $from_user ) && ! empty( $from_user ) ) {
		$user_from = get_userdata( $from_user );
		$builtin['{USERPRO_FROM_NAME}'] = $user_from->user_login;
	}
	
	if ( isset( $form ) && $form != '' ) {
		$profile_fields = $userpro->extract_profile_for_mail( $user->ID, $form );
		$builtin['{USERPRO_PROFILE_FIELDS}'] = $profile_fields['output'];
		$builtin = array_merge( $builtin, $profile_fields['custom_fields'] );
	}
	
	$search = array_keys( $builtin );
	$replace = array_values( $builtin );
	
	$headers = 'From: ' . userpro_get_option( 'mail_from_name' ) . ' <' . userpro_get_option( 'mail_from' ) . '>' .
		 "\r\n";
	
	// ///////////////////////////////////////////////////////
	/* new post email notification for followers */
	// ///////////////////////////////////////////////////////
	if ( $template == 'new_post' ) {
		if ( userpro_get_option( 'enable_html_notifications' ) ) {
			$html = userpro_get_html_email( 'social/new_post' );
			$message = $html;
		} else {
			$message = nl2br( userpro_sc_get_option( 'mail_new_post_follow_m' ) );
		}
		$subject = userpro_sc_get_option( 'mail_new_post_follow_s' );
		$subject = str_replace( $search, $replace, $subject );
		$message = str_replace( $search, $replace, $message );
	}
	
	// ///////////////////////////////////////////////////////
	/* verify email/new registration */
	// ///////////////////////////////////////////////////////
	if ( $template == 'verifyemail' ) {
		if ( userpro_get_option( 'enable_html_notifications' ) ) {
			$html = userpro_get_html_email( 'verifyemail' );
			$message = $html;
		} else {
			if( __('new_verify_mail', 'userpro') != "" && __('new_verify_mail', 'userpro') != "new_verify_mail"){
				$message = __('new_verify_mail', 'userpro');
			} 
			else{
			 	$message = nl2br(userpro_get_option('mail_verifyemail') );
			}
		}
		$subject = userpro_get_option( 'mail_verifyemail_s' );
		$subject = str_replace( $search, $replace, $subject );
		$message = str_replace( $search, $replace, $message );
	}
	
	// ///////////////////////////////////////////////////////
	/* verify email/new registration */
	// ///////////////////////////////////////////////////////
	if ( $template == 'verifyemail_change' ) {
		if ( userpro_get_option( 'enable_html_notifications' ) ) {
			$html = userpro_get_html_email( 'verifyemail_change' );
			$message = $html;
		} else {
			if( __('new_changemail_mail', 'userpro') != "" && __('new_changemail_mail', 'userpro') != "new_changemail_mail"){
				$message = __('new_changemail_mail', 'userpro');
			} 
			else{
	 			$message = nl2br( userpro_get_option( 'mail_verifyemail_change' ) );
			}
		}
		$subject = userpro_get_option( 'mail_verifyemail_change_s' );
		$subject = str_replace( $search, $replace, $subject );
		$message = str_replace( $search, $replace, $message );
	}

	// ///////////////////////////////////////////////////////
	/* secret key request */
	// ///////////////////////////////////////////////////////
	if ( $template == 'secretkey' ) {
		if ( userpro_get_option( 'enable_html_notifications' ) ) {
			$html = userpro_get_html_email( 'secretkey' );
			$message = $html;
		} else {
			if( __('new_secretkey_mail', 'userpro') != "" && __('new_secretkey_mail', 'userpro') != "new_secretkey_mail"){
				$message = __('new_secretkey_mail', 'userpro');
			} 
			else{
	 			$message = nl2br( userpro_get_option( 'mail_secretkey' ) );
			}
			
		}
		$subject = userpro_get_option( 'mail_secretkey_s' );
		$subject = str_replace( $search, $replace, $subject );
		$message = str_replace( $search, $replace, $message );
	}

	if ( $template == 'reset_mail' ) {
		if ( userpro_get_option( 'enable_html_notifications' ) ) {
			$html = userpro_get_html_email( 'reset_mail');
			$message = $html;
		} else {
			if( __('new_reset_mail', 'userpro') != "" && __('new_reset_mail', 'userpro') != "new_reset_mail"){
				$message = __('new_reset_mail', 'userpro');
			} 
			else{
			 $message = nl2br( userpro_get_option( 'reset_password_mail_c' ) );
			}

		}
		$subject = userpro_get_option( 'reset_password_mail_s' );
		$subject = str_replace( $search, $replace, $subject );
		$message = str_replace( $search, $replace, $message );
	}
	// ///////////////////////////////////////////////////////
	/* account being removed */
	// ///////////////////////////////////////////////////////
	if ( $template == 'accountdeleted' ) {
		if ( userpro_get_option( 'enable_html_notifications' ) ) {
			$html = userpro_get_html_email( 'accountdeleted' );
			$message = $html;
		} else {
			if( __('new_accountremoval_mail', 'userpro') != "" && __('new_accountremoval_mail', 'userpro') != "new_accountremoval_mail"){
				$message = __('new_accountremoval_mail', 'userpro');
			} 
			else{
	 			$message = nl2br( userpro_get_option( 'mail_accountdeleted' ) );
			}
		}
		$subject = userpro_get_option( 'mail_accountdeleted_s' );
		$subject = str_replace( $search, $replace, $subject );
		$message = str_replace( $search, $replace, $message );
	}
	// ///////////////////////////////////////////////////////
	/* verification invite */
	// ///////////////////////////////////////////////////////
	if ( $template == 'verifyinvite' ) {
		if ( userpro_get_option( 'enable_html_notifications' ) ) {
			$html = userpro_get_html_email( 'verifyinvite' );
			$message = $html;
		} else {
			$message = nl2br( userpro_get_option( 'mail_verifyinvite' ) );
		}
		$subject = userpro_get_option( 'mail_verifyinvite_s' );
		$subject = str_replace( $search, $replace, $subject );
		$message = str_replace( $search, $replace, $message );
	}
	// ///////////////////////////////////////////////////////
	/* account being verified */
	// ///////////////////////////////////////////////////////
	if ( $template == 'accountverified' ) {
		if ( userpro_get_option( 'enable_html_notifications' ) ) {
			$html = userpro_get_html_email( 'accountverified' );
			$message = $html;
		} else {
			$message = nl2br( userpro_get_option( 'mail_accountverified' ) );
		}
		$subject = userpro_get_option( 'mail_accountverified_s' );
		$subject = str_replace( $search, $replace, $subject );
		$message = str_replace( $search, $replace, $message );
	}
	// ///////////////////////////////////////////////////////
	/* account being unverified */
	// ///////////////////////////////////////////////////////
	if ( $template == 'accountunverified' ) {
		if ( userpro_get_option( 'enable_html_notifications' ) ) {
			$html = userpro_get_html_email( 'accountunverified' );
			$message = $html;
		} else {
			$message = nl2br( userpro_get_option( 'mail_accountunverified' ) );
		}
		$subject = userpro_get_option( 'mail_accountunverified_s' );
		$subject = str_replace( $search, $replace, $subject );
		$message = str_replace( $search, $replace, $message );
	}
	// ///////////////////////////////////////////////////////
	/* account being blocked */
	// ///////////////////////////////////////////////////////
	if ( $template == 'accountblocked' ) {
		if ( userpro_get_option( 'enable_html_notifications' ) ) {
			$html = userpro_get_html_email( 'accountblocked' );
			$message = $html;
		} else {
			$message = nl2br( userpro_get_option( 'mail_accountblocked' ) );
		}
		$subject = userpro_get_option( 'mail_accountblocked_s' );
		$subject = str_replace( $search, $replace, $subject );
		$message = str_replace( $search, $replace, $message );
	}
	
	// ///////////////////////////////////////////////////////
	/* account being unblocked */
	// ///////////////////////////////////////////////////////
	if ( $template == 'accountunblocked' ) {
		if ( userpro_get_option( 'enable_html_notifications' ) ) {
			$html = userpro_get_html_email( 'accountunblocked' );
			$message = $html;
		} else {
			$message = nl2br( userpro_get_option( 'mail_accountunblocked' ) );
		}
		$subject = userpro_get_option( 'mail_accountunblocked_s' );
		$subject = str_replace( $search, $replace, $subject );
		$message = str_replace( $search, $replace, $message );
	}
	
	// ///////////////////////////////////////////////////////
	/* new user's account */
	// ///////////////////////////////////////////////////////
	if ( $template == 'newaccount' && ! $userpro->is_pending( $user->ID ) ) {
		if ( userpro_get_option( 'new_user_notification' ) == '1' ) {
			if ( userpro_get_option( 'enable_html_notifications' ) ) {
				$html = userpro_get_html_email( 'newaccount' );
				$message = $html;
			} else {
				if( __('new_welcome_mail', 'userpro') != "" && __('new_welcome_mail', 'userpro') != "new_welcome_mail"){
					$message = __('new_welcome_mail', 'userpro');
				} 
				else{
	 				$message = nl2br( userpro_get_option( 'mail_newaccount' ) );
				}
			}
			$subject = userpro_get_option( 'mail_newaccount_s' );
			$subject = str_replace( $search, $replace, $subject );
			$message = str_replace( $search, $replace, $message );
		}
	}
	if ( $template == "passwordchange" ) {
		if ( userpro_get_option( 'enable_html_notifications' ) ) {
			$html = userpro_get_html_email( 'passwordchange' );
			$message = $html;
		} else {
			if( __('new_passwordchange_mail', 'userpro') != "" && __('new_passwordchange_mail', 'userpro') != "new_passwordchange_mail"){
				$message = __('new_passwordchange_mail', 'userpro');
			} 
			else{
 				$message = nl2br( userpro_get_option( 'mail_password_change' ) );
			}	
		}
		$subject = userpro_get_option( 'mail_password_change_s' );
		$subject = str_replace( $search, $replace, $subject );
		$message = str_replace( $search, $replace, $message );
	}
	
	// ///////////////////////////////////////////////////////
	/* email user except: profileupdate */
	// ///////////////////////////////////////////////////////
	if ( $template != 'profileupdate' && $template != 'pendingapprove' && $template != 'userpro_connect_request' ) {
		$message = html_entity_decode( nl2br( $message ) );
		wp_mail( $user->user_email, $subject, $message, $headers );
	}
	if ( $template == 'pendingapprove' ) {
		if ( userpro_get_option( 'notify_account_pendingfor_adminapproval' ) == '1' ) {
			if ( userpro_get_option( 'enable_html_notifications' ) ) {
				$html = userpro_get_html_email( 'admin_approval' );
				$message = $html;
			} else {
				if( __('new_accountpending_mail', 'userpro') != "" && __('new_accountpending_mail', 'userpro') != "new_accountpending_mail"){
					$message = __('new_accountpending_mail', 'userpro');
				} 
				else{
	 				$message = userpro_get_option( 'pending_for_admin_approval_txt' );
					$message = html_entity_decode( nl2br( $message ) );
				}	
			}
			$subject = userpro_get_option( 'pending_for_admin_approval' );
			$subject = str_replace( $search, $replace, $subject );
			$message = str_replace( $search, $replace, $message );
			wp_mail( $user->user_email, $subject, $message, $headers );
		}
	}
	if ( $template == "userpro_connect_request" ) {
		$user = get_userdata( $id );
		if ( userpro_get_option( 'enable_html_notifications' ) ) {
			$html = userpro_get_html_email( 'userpro_connect_request' );
			$message = $html;
		} else {
			$message = nl2br( userpro_get_option( 'mail_userpro_connect_request' ) );
		}
		$subject = userpro_get_option( 'mail_userpro_connect_request_s' );
		$subject = str_replace( $search, $replace, $subject );
		$message = str_replace( $search, $replace, $message );
		wp_mail( $user->user_email, $subject, $message, $headers );
	}
	
	// ///////////////////////////////////////////////////////
	/* admin emails notifications */
	// ///////////////////////////////////////////////////////
	if ( $template == 'verifyuser' ) {
		if ( userpro_get_option( 'enable_html_notifications' ) ) {
			$html = userpro_get_html_email( 'admin/admin_verify_request' );
			$message = $html;
		} else {
			$message = userpro_get_option( 'mail_admin_verify_requests' );
			$message = html_entity_decode( nl2br( $message ) );
		}
		$subject = userpro_get_option( 'mail_admin_verify_request' );
		$subject = str_replace( $search, $replace, $subject );
		$message = str_replace( $search, $replace, $message );
		wp_mail( userpro_get_option( 'mail_from' ), $subject, $message, $headers );
	}
	if ( $template == 'pendingapprove' ) {
		if ( userpro_get_option( 'enable_html_notifications' ) ) {
			$html = userpro_get_html_email( 'admin/pendingapprove' );
			$message = $html;
		} else {
			$message = userpro_get_option( 'mail_admin_pendingapprove' );
			$message = html_entity_decode( nl2br( $message ) );
		}
		$subject = userpro_get_option( 'mail_admin_pendingapprove_s' );
		$subject = str_replace( $search, $replace, $subject );
		$message = str_replace( $search, $replace, $message );
		wp_mail( userpro_get_option( 'mail_from' ), $subject, $message, $headers );
	}
	
	if ( $template == 'newaccount' && userpro_get_option( 'notify_admin_new_registration' ) ) {
		if ( userpro_get_option( 'enable_html_notifications' ) ) {
			$html = userpro_get_html_email( 'admin/newaccount' );
			$message = $html;
		} else {
			$message = userpro_get_option( 'mail_admin_newaccount' );
			$message = html_entity_decode( nl2br( $message ) );
		}
		$subject = userpro_get_option( 'mail_admin_newaccount_s' );
		$subject = str_replace( $search, $replace, $subject );
		$message = str_replace( $search, $replace, $message );
		wp_mail( userpro_get_option( 'mail_from' ), $subject, $message, $headers );
	}
	
	if ( $template == 'accountdeleted' && userpro_get_option( 'notify_admin_profile_remove' ) ) {
		if ( userpro_get_option( 'enable_html_notifications' ) ) {
			$html = userpro_get_html_email( 'admin/accountdeleted' );
			$message = $html;
		} else {
			$message = userpro_get_option( 'mail_admin_accountdeleted' );
			$message = html_entity_decode( nl2br( $message ) );
		}
		$subject = userpro_get_option( 'mail_admin_accountdeleted_s' );
		$subject = str_replace( $search, $replace, $subject );
		$message = str_replace( $search, $replace, $message );
		wp_mail( userpro_get_option( 'mail_from' ), $subject, $message, $headers );
	}
	
	if ( $template == 'profileupdate' ) {
		if ( userpro_get_option( 'enable_html_notifications' ) ) {
			$html = userpro_get_html_email( 'admin/profileupdate' );
			$message = $html;
		} else {
			$message = userpro_get_option( 'mail_admin_profileupdate' );
			$message = html_entity_decode( nl2br( $message ) );
		}
		$subject = userpro_get_option( 'mail_admin_profileupdate_s' );
		$subject = str_replace( $search, $replace, $subject );
		$message = str_replace( $search, $replace, $message );
		wp_mail( userpro_get_option( 'mail_from' ), $subject, $message, $headers );
	}
	
	if ( $template == 'verifyemailadmin' ) {
		if ( userpro_get_option( 'enable_html_notifications' ) ) {
			$html = userpro_get_html_email( 'admin/verifyemailadmin' );
			$message = $html;
		} else {
			$message = userpro_get_option( 'mail_verifyemail_admin' );
			$message = html_entity_decode( nl2br( $message ) );
		}
		$subject = userpro_get_option( 'mail_verifyemail_waiting_admin' );
		$subject = str_replace( $search, $replace, $subject );
		$message = str_replace( $search, $replace, $message );
		wp_mail( userpro_get_option( 'mail_from' ), $subject, $message, $headers );
	}
}

function userpro_get_html_email( $template ) {
	/************** Get Content Template ********************/
	ob_start();
	if ( locate_template( 'userpro/email-templates/' . $template . '.html' ) != '' ) {
		include get_stylesheet_directory() . '/userpro/email-templates/' . $template . '.html';
	} else {
		include userpro_path . 'email-templates/' . $template . '.html';
	}
	
	$html = ob_get_contents();
	ob_end_clean();
	
	return $html;
}