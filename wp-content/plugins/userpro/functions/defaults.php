<?php

/* get plugin version */
function userpro_version()
{

				$plugin_data    = get_plugin_data( userpro_path . 'index.php' );
				$plugin_version = $plugin_data[ 'Version' ];
				$plugin_version = str_replace( '.', '', $plugin_version );

				return $plugin_version;
}

/* check if update is installed */
function userpro_update_installed( $ver )
{

				if ( get_option( "userpro_update_" . $ver ) ) {
								return true;
				}

				return false;
}

/* get a global option */
function userpro_get_option($option)
{

    $userpro_default_options = userpro_default_options();
    $settings = get_option('userpro');

    switch ($option) {

        default:
            if (isset($settings[$option])) {
                return $settings[$option];
            } else {
                if (isset($userpro_default_options[$option])) {
                    return $userpro_default_options[$option];
                }
            }
            break;
    }
}

/* set a global option */
function userpro_set_option( $option, $newvalue )
{

				$settings            = get_option( 'userpro' );
				$settings[ $option ] = $newvalue;
				update_option( 'userpro', $settings );
}

/* default options */
function userpro_default_options()
{

				$mail_password_change = __( 'Hi there,' ) . "\r\n\r\n";
				$mail_password_change .= __( "Your password has been updated", "userpro" ) . "\r\n\r\n";
				$mail_password_change .= __( "Your new Password:{VAR1}", "userpro" ) . "\r\n\r\n";
				$mail_password_change .= __( 'If you have any problems, please contact us at {USERPRO_ADMIN_EMAIL}.',
				                             'userpro' ) . "\r\n\r\n";
				$mail_password_change .= __( 'Best Regards!', 'userpro' );
				$mail_secretkey       = __( 'Hi there,' ) . "\r\n\r\n";
				$mail_secretkey       .= __( "You or someone else has requested to change password for this account.",
				                             "userpro" ) . "\r\n\r\n";
				$mail_secretkey       .= __( "The following key was generated to you to be able to change your passsword. Login to our site and attempt to Change your Password and use that key to change your password successfully.",
				                             "userpro" ) . "\r\n\r\n";
				$mail_secretkey       .= __( 'Secret Key: {VAR1}', 'userpro' ) . "\r\n\r\n";
				$mail_secretkey       .= __( 'If you have any problems, please contact us at {USERPRO_ADMIN_EMAIL}.',
				                             'userpro' ) . "\r\n\r\n";
				$mail_secretkey       .= __( 'Best Regards!', 'userpro' );

				$mail_verifyemail = __( 'Hi there,' ) . "\r\n\r\n";
				$mail_verifyemail .= __( "Thanks for signing up at {USERPRO_BLOGNAME}. You must confirm/validate your account before logging in.",
				                         "userpro" ) . "\r\n\r\n";
				$mail_verifyemail .= __( "Please click on the following link to successfully activate your account:",
				                         "userpro" ) . "\r\n";
				$mail_verifyemail .= "{USERPRO_VALIDATE_URL}" . "\r\n\r\n";
				$mail_verifyemail .= __( 'If you have any problems, please contact us at {USERPRO_ADMIN_EMAIL}.',
				                         'userpro' ) . "\r\n\r\n";
				$mail_verifyemail .= __( 'Best Regards!', 'userpro' );

				$mail_verifyemail_change = __( 'Hi there,' ) . "\r\n\r\n";
				$mail_verifyemail_change .= __( "You have changed your email address at {USERPRO_BLOGNAME}. You must confirm/validate your account before logging in.",
				                                "userpro" ) . "\r\n\r\n";
				$mail_verifyemail_change .= __( "Please click on the following link to successfully activate your account:",
				                                "userpro" ) . "\r\n";
				$mail_verifyemail_change .= "{USERPRO_VALIDATE_URL}" . "\r\n\r\n";
				$mail_verifyemail_change .= __( 'If you have any problems, please contact us at {USERPRO_ADMIN_EMAIL}.',
				                                'userpro' ) . "\r\n\r\n";
				$mail_verifyemail_change .= __( 'Best Regards!', 'userpro' );

				$mail_newaccount = __( 'Hi there,' ) . "\r\n\r\n";
				$mail_newaccount .= __( "Thanks for registering. Your account is now active.", "userpro" ) . "\r\n\r\n";
				$mail_newaccount .= __( "To login please visit the following URL:", "userpro" ) . "\r\n";
				$mail_newaccount .= "{USERPRO_LOGIN_URL}" . "\r\n\r\n";
				$mail_newaccount .= __( 'Your account e-mail: {USERPRO_EMAIL}', 'userpro' ) . "\r\n";
				$mail_newaccount .= __( 'Your account username: {USERPRO_USERNAME}', 'userpro' ) . "\r\n";
				$mail_newaccount .= __( 'Your account password: {VAR1}', 'userpro' ) . "\r\n\r\n";
				$mail_newaccount .= __( 'If you have any problems, please contact us at {USERPRO_ADMIN_EMAIL}.',
				                        'userpro' ) . "\r\n\r\n";
				$mail_newaccount .= __( 'Best Regards!', 'userpro' );

				$mail_verifyinvite = __( 'Hi there,' ) . "\r\n\r\n";
				$mail_verifyinvite .= __( "This is an invitation to get verified at {USERPRO_BLOGNAME}.", "userpro" ) . "\r\n\r\n";
				$mail_verifyinvite .= __( "To accept this invitation and get verified instantly, please click the following link:",
				                          "userpro" ) . "\r\n";
				$mail_verifyinvite .= "{USERPRO_ACCEPT_VERIFY_INVITE}" . "\r\n\r\n";
				$mail_verifyinvite .= __( 'If you do not want to GET VERIFIED, please ignore this email. No further action is required.',
				                          'userpro' ) . "\r\n\r\n";
				$mail_verifyinvite .= __( 'If you have any further questions, please contact us at {USERPRO_ADMIN_EMAIL}.',
				                          'userpro' ) . "\r\n\r\n";
				$mail_verifyinvite .= __( 'Best Regards!', 'userpro' );

				$mail_accountdeleted = __( 'Hi there,' ) . "\r\n\r\n";
				$mail_accountdeleted .= __( "Your account has been deleted from {USERPRO_BLOGNAME}.", "userpro" ) . "\r\n\r\n";
				$mail_accountdeleted .= __( 'If you have any further questions, please contact us at {USERPRO_ADMIN_EMAIL}.',
				                            'userpro' ) . "\r\n\r\n";
				$mail_accountdeleted .= __( 'Best Regards!', 'userpro' );

				$mail_accountverified = __( 'Hi there,' ) . "\r\n\r\n";
				$mail_accountverified .= __( "Your account is now verified at {USERPRO_BLOGNAME}.", "userpro" ) . "\r\n\r\n";
				$mail_accountverified .= __( 'If you have any further questions, please contact us at {USERPRO_ADMIN_EMAIL}.',
				                             'userpro' ) . "\r\n\r\n";
				$mail_accountverified .= __( 'Best Regards!', 'userpro' );

				$mail_accountunverified = __( 'Hi there,' ) . "\r\n\r\n";
				$mail_accountunverified .= __( "We apologize. Your account is no longer verified at {USERPRO_BLOGNAME}.",
				                               "userpro" ) . "\r\n\r\n";
				$mail_accountunverified .= __( 'If you have any further questions, please contact us at {USERPRO_ADMIN_EMAIL}.',
				                               'userpro' ) . "\r\n\r\n";
				$mail_accountunverified .= __( 'Best Regards!', 'userpro' );

				$mail_accountblocked = __( 'Hi there,' ) . "\r\n\r\n";
				$mail_accountblocked .= __( "Your account is now blocked at {USERPRO_BLOGNAME}.", "userpro" ) . "\r\n\r\n";
				$mail_accountblocked .= __( 'If you have any query, please contact us at {USERPRO_ADMIN_EMAIL}.',
				                            'userpro' ) . "\r\n\r\n";
				$mail_accountblocked .= __( 'Best Regards!', 'userpro' );

				$mail_accountunblocked = __( 'Hi there,' ) . "\r\n\r\n";
				$mail_accountunblocked .= __( "Your account is now unblocked.", "userpro" ) . "\r\n\r\n";
				$mail_accountunblocked .= __( 'If you have any query, please contact us at {USERPRO_ADMIN_EMAIL}.',
				                              'userpro' ) . "\r\n\r\n";
				$mail_accountunblocked .= __( 'Best Regards!', 'userpro' );

				$pending_for_admin_approval_txt = __( 'Hi there,' ) . "\r\n\r\n";
				$pending_for_admin_approval_txt .= __( "Your account is currently being reviewed at {USERPRO_BLOGNAME}.",
				                                       "userpro" ) . "\r\n\r\n";
				$pending_for_admin_approval_txt .= __( 'If you have any query, please contact us at {USERPRO_ADMIN_EMAIL}.',
				                                       'userpro' ) . "\r\n\r\n";
				$pending_for_admin_approval_txt .= __( 'Best Regards!', 'userpro' );

				$mail_admin_pendingapprove = __( 'Hi there,' ) . "\r\n\r\n";
				$mail_admin_pendingapprove .= __( "{USERPRO_USERNAME} has just created a new account at {USERPRO_BLOGNAME}. The account is pending your manual review.",
				                                  "userpro" ) . "\r\n\r\n";
				$mail_admin_pendingapprove .= __( "To approve/reject new user registrations, please click the following link:",
				                                  "userpro" ) . "\r\n";
				$mail_admin_pendingapprove .= "{USERPRO_PENDING_REQUESTS_URL}" . "\r\n\r\n";
				$mail_admin_pendingapprove .= __( 'This is an automated notification that was sent to you by UserPro. No further action is needed.',
				                                  'userpro' );

				$mail_admin_verify_requests = __( 'Hi there,' ) . "\r\n\r\n";
				$mail_admin_verify_requests .= __( "{USERPRO_USERNAME} has just sent you account verify request at {USERPRO_BLOGNAME}.",
				                                   "userpro" ) . "\r\n\r\n";
				$mail_admin_verify_requests .= __( "You can check his profile via the following link:", "userpro" ) . "\r\n";
				$mail_admin_verify_requests .= "{USERPRO_PROFILE_LINK}" . "\r\n\r\n";
				$mail_admin_verify_requests .= __( 'This is an automated notification that was sent to you by UserPro. No further action is needed.',
				                                   'userpro' );

				$mail_userpro_connect_request = __( 'Hi there,' ) . "\r\n\r\n";
				$mail_userpro_connect_request .= __( "{USERPRO_USERNAME} has just sent you connect request at {USERPRO_BLOGNAME}.",
				                                     "userpro" ) . "\r\n\r\n";
				$mail_userpro_connect_request .= __( "You can check his profile via the following link:", "userpro" ) . "\r\n";
				$mail_userpro_connect_request .= "{USERPRO_PROFILE_LINK}" . "\r\n\r\n";
				$mail_userpro_connect_request .= __( 'This is an automated notification that was sent to you by UserPro. No further action is needed.',
				                                     'userpro' );

				$mail_admin_newaccount = __( 'Hi there,' ) . "\r\n\r\n";
				$mail_admin_newaccount .= __( "{USERPRO_USERNAME} has just created a new account at {USERPRO_BLOGNAME}.",
				                              "userpro" ) . "\r\n\r\n";
				$mail_admin_newaccount .= __( "You can check his profile via the following link:", "userpro" ) . "\r\n";
				$mail_admin_newaccount .= "{USERPRO_PROFILE_LINK}" . "\r\n\r\n";
				$mail_admin_newaccount .= __( 'This is an automated notification that was sent to you by UserPro. No further action is needed.',
				                              'userpro' );

				$mail_admin_accountdeleted = __( 'Hi there,' ) . "\r\n\r\n";

				$mail_admin_accountdeleted .= __( "{USERPRO_USERNAME}'s profile has been just deleted from {USERPRO_BLOGNAME}.",
				                                  "userpro" ) . "\r\n\r\n";
				$mail_admin_accountdeleted .= __( 'This is an automated notification that was sent to you by UserPro. No further action is needed.',
				                                  'userpro' );

				$mail_admin_profileupdate = __( 'Hi there,' ) . "\r\n\r\n";
				$mail_admin_profileupdate .= __( "{USERPRO_USERNAME} has just updated their profile at {USERPRO_BLOGNAME}.",
				                                 "userpro" ) . "\r\n\r\n";
				$mail_admin_profileupdate .= __( "To view his/her profile:", "userpro" ) . "\r\n";
				$mail_admin_profileupdate .= "{USERPRO_PROFILE_LINK}" . "\r\n\r\n";
				$mail_admin_profileupdate .= __( 'This is an automated notification that was sent to you by UserPro. No further action is needed.',
				                                 'userpro' );

				$reset_password_email = __( 'Hi there,' ) . "\r\n\r\n";
				$reset_password_email .= __( 'Please click below link to reset your password,' ) . "\r\n\r\n";
				$reset_password_email .= __( '<a href="{USERPRO_LOGIN_URL}?a=reset&sk={VAR1}">Click here</a>' );

				$mail_verifyemail_admin = __( 'Hi there,' ) . "\r\n\r\n";
				$mail_verifyemail_admin .= __( "{USERPRO_USERNAME} has just created a new account at {USERPRO_BLOGNAME}. The account is pending email verification.",
				                               "userpro" ) . "\r\n\r\n";
				$mail_verifyemail_admin .= __( 'This is an automated notification that was sent to you by UserPro. No further action is needed.',
				                               'userpro' );

				$array[ 'roles_can_edit_profiles' ]      = 'none';
				$array[ 'show_badges_profile' ]          = '1';
				$array[ 'twitter_fix' ]                  = 'b';
				$array[ 'new_user_notification' ]        = 1;
				$array[ 'redirect_author_to_profile' ]   = 0;
				$array[ 'ppfix' ]                        = 'a';
				$array[ 'pimg' ]                         = 0;
				$array[ 'hide_online_admin' ]            = 0;
				$array[ 'max_file_size' ]                = 8388608;
				$array[ 'allowed_roles' ]                = [ 'subscriber' ];
				$array[ 'dashboard_redirect_users' ]     = 0;
				$array[ 'profile_redirect_users' ]       = 0;
				$array[ 'login_redirect_users' ]         = 0;
				$array[ 'register_redirect_users' ]      = 0;
				$array[ 'dashboard_redirect_users_url' ] = '';
				$array[ 'profile_redirect_users_url' ]   = '';
				$array[ 'login_redirect_users_url' ]     = '';
				$array[ 'register_redirect_users_url' ]  = '';
				$array[ 'after_login' ]                  = 'profile';
				$array[ 'show_admin_after_login' ]       = 1;
				$array[ 'after_register_autologin' ]     = 1;
				$array[ 'after_register' ]               = 'profile';
				$array[ 'allow_guests_view_profiles' ]   = 1;
				$array[ 'allow_users_view_profiles' ]    = 1;
				$array[ 'slug' ]                         = 'profile';
				$array[ 'slug_register' ]                = 'register';
				$array[ 'slug_edit' ]                    = 'edit';
				$array[ 'slug_login' ]                   = 'login';
				$array[ 'slug_logout' ]                  = 'logout';
				$array[ 'slug_directory' ]               = 'members';
				$array[ 'slug_connections' ]             = 'connections';
				$array[ 'logout_uri' ]                   = 1;
				$array[ 'logout_uri_custom' ]            = '';
				$array[ 'enable_connect' ]               = 'n';
				$array[ 'mail_from_name' ]               = get_bloginfo( 'name' );
				$array[ 'mail_from' ]                    = get_option( 'admin_email' );
				$array[ 'enable_html_notifications' ]    = 0;

				$array[ 'mail_password_change_s' ] = __( 'Password Updated', 'userpro' );
				$array[ 'mail_password_change' ]   = $mail_password_change;
				$array[ 'mail_secretkey_s' ]       = __( 'Reset Your Password', 'userpro' );
				$array[ 'mail_secretkey' ]         = $mail_secretkey;

				$array[ 'mail_verifyemail_s' ] = __( 'Verify your Account', 'userpro' );
				$array[ 'mail_verifyemail' ]   = $mail_verifyemail;

				$array[ 'mail_verifyemail_change_s' ] = __( 'Reverify your Account after Email change', 'userpro' );
				$array[ 'mail_verifyemail_change' ]   = $mail_verifyemail_change;

				$array[ 'mail_newaccount_s' ] = sprintf( __( 'Welcome to %s!', 'userpro' ), get_bloginfo( 'name' ) );
				$array[ 'mail_newaccount' ]   = $mail_newaccount;

				$array[ 'mail_accountdeleted_s' ] = __( 'Your profile has been removed!', 'userpro' );
				$array[ 'mail_accountdeleted' ]   = $mail_accountdeleted;

				$array[ 'mail_userpro_connect_request_s' ] = __( 'New Connect Request!', 'userpro' );
				$array[ 'mail_userpro_connect_request' ]   = $mail_userpro_connect_request;

				$array[ 'mail_verifyinvite_s' ] = sprintf( __( 'Get Verified at %s!', 'userpro' ), get_bloginfo( 'name' ) );
				$array[ 'mail_verifyinvite' ]   = $mail_verifyinvite;

				$array[ 'mail_admin_verify_request' ]  = __( 'New verification Request', 'userpro' );
				$array[ 'mail_admin_verify_requests' ] = $mail_admin_verify_requests;
				$array[ 'mail_accountverified_s' ]     = __( 'Your account is now verified!', 'userpro' );
				$array[ 'mail_accountverified' ]       = $mail_accountverified;

				$array[ 'mail_accountunverified_s' ] = __( 'Your account is no longer verified!', 'userpro' );
				$array[ 'mail_accountunverified' ]   = $mail_accountunverified;

				$array[ 'pending_for_admin_approval' ]     = __( 'Your account is Pending for Manual Review', 'userpro' );
				$array[ 'pending_for_admin_approval_txt' ] = $pending_for_admin_approval_txt;

				$array[ 'mail_accountblocked_s' ] = __( 'Your account is now blocked!', 'userpro' );
				$array[ 'mail_accountblocked' ]   = $mail_accountblocked;

				$array[ 'mail_accountunblocked_s' ] = __( 'Your account is now unblocked!', 'userpro' );
				$array[ 'mail_accountunblocked' ]   = $mail_accountunblocked;

				$array[ 'mail_admin_pendingapprove_s' ] = __( '[UserPro] User awaiting manual review', 'userpro' );
				$array[ 'mail_admin_pendingapprove' ]   = $mail_admin_pendingapprove;

				$array[ 'mail_admin_newaccount_s' ] = __( '[UserPro] New User Registration', 'userpro' );
				$array[ 'mail_admin_newaccount' ]   = $mail_admin_newaccount;

				$array[ 'mail_admin_accountdeleted_s' ] = __( '[UserPro] A profile has been removed!', 'userpro' );
				$array[ 'mail_admin_accountdeleted' ]   = $mail_admin_accountdeleted;

				$array[ 'mail_admin_profileupdate_s' ] = __( '[UserPro] A profile has been updated!', 'userpro' );
				$array[ 'mail_admin_profileupdate' ]   = $mail_admin_profileupdate;

				$array[ 'reset_password_mail_s' ] = __( 'Reset your password' );
				$array[ 'reset_password_mail_c' ] = $reset_password_email;

				$array[ 'notify_user_password_update' ]             = 1;
				$array[ 'notify_user_verified' ]                    = 1;
				$array[ 'show_filter' ]                             = 1;
				$array[ 'notify_user_unverified' ]                  = 1;
				$array[ 'notify_admin_profile_save' ]               = 1;
				$array[ 'notify_admin_profile_remove' ]             = 1;
				$array[ 'user_can_delete_profile' ]                 = 1;
				$array[ 'skin' ]                                    = 'elegant';
				$array[ 'layout' ]                                  = 'float';
				$array[ 'modern_layout' ]                           = 0;
				$array[ 'facebook_app_id' ]                         = '';
				$array[ 'facebook_connect' ]                        = 1;
				$array[ 'notify_account_pendingfor_adminapproval' ] = 0;
				$array[ 'notify_admin_new_registration' ]           = 1;
				$array[ 'notify_admin_email_approve' ]              = 0;
				/**
					* Facebook Autopost Bring Back
					* Added By Rahul
					*/
				$array[ 'facebook_autopost' ]             = 0;
				$array[ 'facebook_autopost_name' ]        = '';
				$array[ 'facebook_autopost_body' ]        = '';
				$array[ 'facebook_autopost_caption' ]     = '';
				$array[ 'facebook_autopost_description' ] = '';
				$array[ 'facebook_autopost_link' ]        = '';

				$array[ 'facebook_publish_autopost' ]             = 0;
				$array[ 'facebook_publish_autopost_name' ]        = '';
				$array[ 'facebook_publish_autopost_body' ]        = '';
				$array[ 'facebook_publish_autopost_caption' ]     = '';
				$array[ 'facebook_publish_autopost_description' ] = '';
				$array[ 'facebook_publish_autopost_link' ]        = '';

				$array[ 'facebook_follow_autopost' ]             = 0;
				$array[ 'facebook_follow_autopost_name' ]        = '';
				$array[ 'facebook_follow_autopost_body' ]        = '';
				$array[ 'facebook_follow_autopost_caption' ]     = '';
				$array[ 'facebook_follow_autopost_description' ] = '';
				$array[ 'facebook_follow_autopost_link' ]        = '';
				/**
					* Facebook Autopost Bring Back
					* Added By Rahul
					*
					*/
				$array[ 'twitter_consumer_key' ]                = '';
				$array[ 'twitter_consumer_secret' ]             = '';
				$array[ 'twitter_connect' ]                     = 1;
				$array[ 'twitter_autopost' ]                    = 0;
				$array[ 'twitter_autopost_msg' ]                = '';
				$array[ 'twitter_signin_redirect' ]             = '';
				$array[ 'google_client_id' ]                    = '';
				$array[ 'google_client_secret' ]                = '';
				$array[ 'google_redirect_uri' ]                 = add_query_arg( 'upslug', 'gplus',
				                                                                 trailingslashit( esc_url( home_url() ) ) );
				$array[ 'google_connect' ]                      = 1;
				$array[ 'google_signup_redirect' ]              = '';
				$array[ 'google_signin_redirect' ]              = '';
				$array[ 'restrict_url' ]                        = trailingslashit( home_url() ) . 'profile/login/';
				$array[ 'default_role' ]                        = 'subscriber';
				$array[ 'restricted_page_verified' ]            = 0;
				$array[ 'show_logout_register' ]                = 1;
				$array[ 'show_logout_login' ]                   = 1;
				$array[ 'use_default_avatars' ]                 = 0;
				$array[ 'reset_admin_pass' ]                    = 1;
				$array[ 'allow_users_verify_request' ]          = 1;
				$array[ 'picture_save_method' ]                 = 'internal';
				$array[ 'admin_user_notices' ]                  = 1;
				$array[ 'show_user_notices' ]                   = 1;
				$array[ 'show_user_notices_him' ]               = 1;
				$array[ 'users_can_register' ]                  = 1;
				$array[ 'width' ]                               = '480px';
				$array[ 'permalink_type' ]                      = 'username';
				$array[ 'users_approve' ]                       = '1';
				$array[ 'hidden_from_view' ]                    = 'display_name,profilepicture,facebook,twitter,google_plus,user_email,user_url,phone_number,custom_profile_color,custom_profile_bg,securityqa,instagram,linkedin,youtube,tags';
				$array[ 'googlefont' ]                          = 'Roboto';
				$array[ 'customfont' ]                          = '';
				$array[ 'field_icons' ]                         = 1;
				$array[ 'hide_admin_bar' ]                      = 1;
				$array[ 'modstate_social' ]                     = 1;
				$array[ 'terms_agree' ]                         = 1;
				$array[ 'terms_agree_text' ]                    = __( 'To complete registration, you must read and agree to our <a href="#">terms and conditions</a>. This text can be custom.',
				                                                      'userpro' );
				$array[ 'restricted_content_text' ]             = __( 'You cannot view this content because It is available to members only. Please {LOGIN_POPUP} or {REGISTER_POPUP} to view this area.',
				                                                      'userpro' );
				$array[ 'verified_link' ]                       = '';
				$array[ 'verified_badge_by_name' ]              = 1;
				$array[ 'use_relative' ]                        = 'relative';
				$array[ 'mailchimp_api' ]                       = '';
				$array[ 'modstate_online' ]                     = 0;
				$array[ 'modstate_showoffline' ]                = 0;
				$array[ 'envato_api' ]                          = '';
				$array[ 'envato_username' ]                     = '';
				$array[ 'thumb_style' ]                         = 'default';
				$array[ 'heading_light' ]                       = 'Light';
				$array[ 'unverify_on_namechange' ]              = 1;
				$array[ 'allow_dash_display_name' ]             = 0;
				$array[ 'homepage_guest_lockout' ]              = '';
				$array[ 'homepage_member_lockout' ]             = '';
				$array[ 'site_guest_lockout' ]                  = 0;
				$array[ 'site_guest_lockout_pageid' ]           = '';
				$array[ 'site_guest_lockout_pageids' ]          = '';
				$array[ 'show_flag_in_profile' ]                = 1;
				$array[ 'show_flag_in_badges' ]                 = 1;
				$array[ 'user_display_name' ]                   = 'display_name';
				$array[ 'user_display_name_key' ]               = '';
				$array[ 'backend_users_change' ]                = 0;
				$array[ 'buddypress_userpro_link_sync' ]        = 0;
				$array[ 'buddypress_userpro_avatar_sync' ]      = 0;
				$array[ 'buddypress_userpro_displayname_sync' ] = 0;
				$array[ 'bbpress_userpro_link_sync' ]           = 0;
				$array[ 'sociallogin' ]                         = 1;
				$array[ 'alphabetical_pagination' ]             = 0;
				$array[ 'allow_dashboard_for_these_roles' ]     = '';
				$array[ 'instant_publish_roles' ]               = '';
				$array[ 'rtl' ]                                 = 0;
				$array[ 'userpro_panic_key' ]                   = 'xZaejn123';
				$array[ 'profile_lightbox' ]                    = 1;
				$array[ 'lightbox' ]                            = 1;
				$array[ 'userpro_css' ]                         = '';
				$array[ 'roles_can_view_profiles' ]             = '';
				$array[ 'disable_activity_log' ]                = 0;
				$array[ 'max_field_length' ]                    = 36;
				$array[ 'max_field_length_active' ]             = 1;
				$array[ 'max_field_length_include' ]            = 'user_login,first_name,last_name,display_name';
				$array[ 'blocked_users' ]                       = '';
				$array[ 'phonefields' ]                         = 'phone_number';
				$array[ 'phonefields_regex' ]                   = '/^\(?\+?[\d\(\-\s\)]+$/i';
				$array[ 'date_format' ]                         = 'dd-mm-yy';
				$array[ 'update_role' ]                         = 'no_role';
				$array[ 'enable_post_editor' ]                  = 'n';
				$array[ 'enable_save_as_draft' ]                = 'n';
				$array[ 'enable_reset_by_mail' ]                = 'n';
				$array[ 'mailchimp_checkbox_condition' ]        = '1';
				$array[ 'up_conditional_menu' ]                 = '0';
				$array[ 'userpro_block_email_domains' ]         = '';
				$array[ 'userpro_allow_email_domains' ]         = '';
				$array[ 'default_background_img' ]              = '';
				$array[ 'min_field_length' ]                    = 1;
				$array[ 'min_field_length_active' ]             = 1;
				$array[ 'min_field_length_include' ]            = 'user_login,first_name,last_name,display_name';

				/**
					* Invite User New Field Added
					* Added By Rahul
					* On 03-12-2014
					*/
				$array[ 'userpro_invite_emails_enable' ]   = 0;
				$array[ 'invite_subject' ]                 = __( 'You are invited to register at ', 'userpro' );
				$array[ 'userpro_invite_emails_template' ] = 'You are invited <a href="{invitelink}"> Click here to register</a>';
				$array[ 'userpro_enable_webcam' ]          = 0;
				$array[ 'mail_verifyemail_admin' ]         = $mail_verifyemail_admin;
				$array[ 'mail_verifyemail_waiting_admin' ] = __( '[UserPro]New Registration - Pending email verification by user',
				                                                 'userpro' );
				$array[ 'up_delete_cache_interval' ]       = 30;

				return  $array;
}

function userpro_selected( $k, $arr )
{

				foreach ( $arr as $key => $val ) {
								$k    = explode( '#', $k );
								$k    = array_shift( $k );
								$name = strtolower( $val[ 'name' ] );
								if ( $k == $name ) {
												return "selected=selected";
								}
				}
}

/* gets a selected value */
function userpro_is_selected( $k, $arr )
{

				if ( isset( $arr ) && is_array( $arr ) && in_array( $k, $arr ) ) {

								echo 'selected="selected"';
				} elseif ( $arr == $k ) {

								echo 'selected="selected"';
				}

}

/* get roles */
function userpro_get_roles( $filter )
{

				if ( !isset( $wp_roles ) ) {
								$wp_roles = new WP_Roles();
				}
				$roles = $wp_roles -> get_names();
				/*
					* Commented by Ranjith to resolve mandatory role field issue
					*/

				//$allowed_roles[0] = __('&mdash; Select account role &mdash;','userpro');
				foreach ( $roles as $k => $v ) {
								if ( in_array( $k, $filter ) ) {
												$allowed_roles[ $k ] = $v;
								}
				}
				if ( current_user_can( 'manage_options' ) ) {
								foreach ( $roles as $k => $v ) {
												$allowed_roles[ $k ] = $v;
								}
				}

				return $allowed_roles;
}
