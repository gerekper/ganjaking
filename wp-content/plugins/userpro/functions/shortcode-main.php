<?php

	/* Registers and display the shortcode */
	add_shortcode('userpro', 'userpro' );
	function userpro( $args=array() ) {
		global $post, $wp, $userpro_admin, $userpro;
                $argument = $args;
		if (is_home()){
			$permalink = home_url();
		} else {
			if (isset($post->ID)){
				$permalink = get_permalink($post->ID);
			} else {
				$permalink = '';
			}
		}
		$userpro->up_enqueue_scripts_styles();
		
		/* arguments */
		
		/* Deafult Args */
		$default_args=array(
				'modal_profile_saved'				=> __('Your profile has been saved!','userpro'),
				'template' 					=> null,
				'max_width'					=> userpro_get_option('width'),
				'uploads_dir'					=> $userpro->get_uploads_url(),
				'default_avatar_male'				=> userpro_url . 'img/default_avatar_male.jpg',
				'default_avatar_female'				=> userpro_url . 'img/default_avatar_female.jpg',
				'layout'					=> userpro_get_option('layout'),
				'margin_top'					=> 0,
				'margin_bottom'					=> '30px',
				'align'						=> 'center',
				'skin'						=> userpro_get_option('skin'),
				'required_text'					=> __('This field is required','userpro'),
				'datepicker_format'					=> __('Bad format','userpro'),
				'password_too_short'				=> __('Your password is too short','userpro'),
				'passwords_do_not_match'			=> __('Passwords do not match','userpro'),
				'password_not_strong'				=> __('Password is not strong enough','userpro'),
				'keep_one_section_open'				=> 0,
				'allow_sections'				=> 1,
				'permalink'					=> $permalink,
				'field_icons'					=> userpro_get_option('field_icons'),
				'profile_thumb_size'				=> 80,					
				'register_heading' 				=> __('Register an Account','userpro'),
				'register_side'					=> __('Already a member?','userpro'),
				'register_side_action'				=> 'login',
				'register_button_action'			=> 'login',
				'register_button_primary'			=> __('Register','userpro'),
				'register_button_secondary'			=> __('Login','userpro'),
				'register_group'				=> 'default',
				'register_redirect'				=> '',
				'type'						=> userpro_mu_get_option('multi_forms_default'),
				'force_redirect_uri'				=> 0,
				'login_heading' 				=> __('Login','userpro'),
				'login_side'					=> __('Forgot your password?','userpro'),
				'login_side_action'				=> 'reset',
				'login_resend'					=> __('Resend verification email?','userpro'),
				'login_resend_action'				=> 'resend',
				'login_button_action'				=> 'register',					
				'login_button_primary'				=> __('Login','userpro'),
				'login_button_secondary'			=> __('Create an Account','userpro'),
				'login_group'					=> 'default',
				'login_redirect'				=> '',
				'rememberme'					=> 'true',					
				'delete_heading'				=> __('Delete Profile','userpro'),
				'delete_side'					=> __('Undo, back to profile','userpro'),
				'delete_side_action'				=> 'view',
				'delete_button_action'				=> 'view',
				'delete_button_primary'				=> __('Confirm Deletion','userpro'),
				'delete_button_secondary'			=> __('Back to Profile','userpro'),
				'delete_group'						=> 'default',					
				'reset_heading'					=> __('Reset Password','userpro'),
				'reset_side'					=> __('Back to Login','userpro'),
				'reset_side_action'				=> 'login',
				'reset_button_action'				=> 'change',
				'reset_button_primary'				=> __('Request Secret Key','userpro'),
				'reset_button_secondary'			=> __('Change your Password','userpro'),
				'reset_group'					=> 'default',
				'resend_heading'				=> __('Resend email','userpro'),
				'resend_side'					=> __('Back to Login','userpro'),
				'resend_side_action'				=> 'login',
				'resend_button_action'				=> 'resend',
				'resend_button_primary'				=> __('Resend verification email','userpro'),
				'reset_group'					=> 'default',						
				'change_heading'				=> __('Change your Password','userpro'),
				'change_side'					=> __('Request New Key','userpro'),
				'change_side_action'				=> 'reset',
				'change_button_action'				=> 'reset',
				'change_button_primary'				=> __('Change my Password','userpro'),
				'change_button_secondary'			=> __('Do not have a secret key?','userpro'),
				'change_group'					=> 'default',					
				'list_heading'					=> __('Latest Members','userpro'),
				'list_per_page'					=> 5,
				'list_sortby'					=> 'registered',
				'list_order'					=> 'desc',
				'list_users'					=> '',
				'list_group'					=> 'default',
				'list_thumb'					=> 50,
				'list_showthumb'				=> 1,
				'list_showsocial'				=> 1,
				'list_showbio'					=> 0,
				'list_verified'					=> 0,
				'list_relation'					=> 'or',
				'list_popup_view'				=> 0,
				'online_heading'				=> __('Who is online now','userpro'),
				'online_thumb'					=> 30,
				'online_showthumb'				=> 1,
				'online_showsocial'				=> 0,
				'online_showbio'				=> 0,
				'online_mini'					=> 1,
				'online_mode'					=> 'vertical',					
				'edit_button_primary'				=> __('Save Changes','userpro'),
				'edit_group'					=> 'default',					
				'view_group'					=> 'default',					
				'social_target'					=> '_blank',
				'social_group'					=> 'default',					
				'card_width'					=> '250px',
				'card_img_width'				=> '250',
				'card_showbio'					=> 1,
				'card_showsocial'				=> 1,
				 'usercanedit'					=> 1,	
				'link_target'					=> '_blank',					
				'error_heading'					=> __('An error has occured','userpro'),
				'memberlist_table'				=> 0,
				'memberlist_table_columns'			=> 'user_id,picture,name,country,gender,role,email_user,message_user',
				'show_on_mobile'				=> 'picture,name,country,email_user,message_user',
				'memberlist_v2'					=> 1,
				'memberlist_v2_pic_size'			=> '86',
				'memberlist_v2_fields'				=> 'age,gender,country',
				'memberlist_v2_bio'				=> 1,
				'memberlist_v2_showbadges'			=> 1,
				'memberlist_v2_showname'			=> 1,
				'memberlist_v2_showsocial'			=> 1,					
				'memberlist_pic_size'				=> '120',
				'memberlist_pic_topspace'			=> '15',
				'memberlist_pic_sidespace'			=> '30',
				'memberlist_pic_rounded'			=> 1,
				'memberlist_width'				=> '100%',
				'memberlist_paginate'				=> 1,
				'memberlist_paginate_top'			=> 1,
				'memberlist_paginate_bottom' 			=> 1,
				'memberlist_show_name'				=> 1,
				'memberlist_popup_view'				=> 0,
				'memberlist_withavatar'				=> 0,
				'memberlist_verified'				=> 0,
				'memberlist_filters'				=> '',
				'memberlist_default_search'			=> 1,
				'memberlist_show_follow'			=> 0,
				'per_page'					=> 12,
				'sortby'					=> 'registered',
				'order'						=> 'desc',
				'relation'					=> 'and',
				'search'					=> 1,
				'exclude'					=> '',
				'show_social'					=> 1,
				'registration_closed_side'			=> __('Existing member? login','userpro'),
				'registration_closed_side_action'		=> 'login',
				'facebook_redirect'				=> 'profile',
				'logout_redirect'				=> '',
				'post_paginate'					=> 1,
				'postsbyuser_num'				=> '12',
				'postsbyuser_types'				=> 'post',
				'postsbyuser_mode'				=> 'grid',
				'postsbyuser_thumb'				=> 50,
				'postsbyuser_showthumb'				=> 1,
				'postsbyuser_taxonomy'				=> 'category',
				'postsbyuser_category'				=> '',
				'following_per_page'                		=> '4',
				'following_paginate'				=> '1',
				'followers_per_page'                		=>'4',
				'followers_paginate'				=> '1',
				'publish_heading'				=> __('Add a New Post','userpro'),
				'publish_button_primary'			=> __('Publish','userpro'),
				'publish_button_draft'				=> __('Save as Draft','userpro'),
		);
		$defaults = apply_filters('userpro_shortcode_args', $default_args);
		$args = wp_parse_args( $args, $defaults );
		/* The arguments are passed via shortcode through admin panel*/
		foreach ($default_args as $key => $val) {
			if(isset($args[$key])) {
				$$key = $args[$key];
			}else {
				$$key = $val;
			}
		}
		if ($template) :
		
		STATIC $i = 0;
	
		ob_start();

		/* increment wall */
		$i = rand(1, 1000);

		/* user template */
		
		do_action('userpro_custom_template_hook', array_merge( $args, array( 'i' => $i ) ) );

if(isset($argument['hide_content']) && $argument['hide_content'] && empty($_GET)){
			if(!empty($argument)){
				$parameters = '';
				$argument_length = count($argument);
				foreach($argument as $key=>$value){
					if($key=='hide_content')
						continue;
					$parameters.=" ".$key."="."$value"; 
				}
			}
			$output = '<div class=userpro_show_content data-parameters="'.$parameters.'" ><a href="#">Click here to view the content</a></div>';
			return $output;
		}
		else if(isset($argument['hide_content']) && $argument['hide_content']){
			$template = $template;
		}
		if( isset($_GET['a']) && $_GET['a']=='reset' ){
				$template = 'change';
		}

		switch($template) {
			
			case 'publish':
				if (userpro_is_logged_in()){
				
					$user_id = get_current_user_id();
					$layout = 'none';$args['layout'] = 'none';
					
					if ( isset($args['deny_roles']) && !empty($args['deny_roles']) ){
						$denied_roles = explode(',',$args['deny_roles']);
						if ($userpro->user_role_in_array($user_id, $denied_roles)){
							if (locate_template('userpro/not_allowed.php') != '') {
								include get_stylesheet_directory() . '/userpro/not_allowed.php';
							} else { 
								include userpro_path . "templates/not_allowed.php";
							}
						} else {
							if (locate_template('userpro/' . $template . '.php') != '') {
								include get_stylesheet_directory() . '/userpro/'. $template . '.php';
							} else {
								include userpro_path . "templates/$template.php";
							}
						}
					} else {

						if (locate_template('userpro/' . $template . '.php') != '') {
							include get_stylesheet_directory() . '/userpro/'. $template . '.php';
						} else {
							include userpro_path . "templates/$template.php";
						}
					
					}
					
				} else {
				
					/* attempt to edit profile so force redirect to same page */
					$args['force_redirect_uri'] = 1;
					add_action('userpro_pre_form_message', 'userpro_msg_login_to_post');
					$template = 'login';$args['template'] = 'login';
					if (locate_template('userpro/' . $template . '.php') != '') {
						include get_stylesheet_directory() . '/userpro/'. $template . '.php';
					} else {
						include userpro_path . "templates/login.php";
					}
					remove_action('userpro_pre_form_message', 'userpro_msg_login_to_post');
					
				}
				break;
			
		
			case 'postsbyuser':
				if (isset($args['user'])){
					if ($args['user'] == 'author') {
						if (is_author()){
							$user_id = get_query_var('author');
						} else {
							$user_id = get_the_author_meta('ID');
						}
					} 
					
					
					else {
						$user_id = userpro_get_view_user( $args['user'], 'shortcode_user' );
					}
				} 
				
				else if(isset($args['user_id']))
				{
					
					$user_id=$args['user_id'];
				}
				else {
					$user_id = userpro_get_view_user( get_query_var('up_username') );
				}
				$totalposts = count_user_posts( $user_id );
				$paginate = paginate_links( array(
						'base'         => add_query_arg('postp' , '%#%'),
						'total'        => ceil($totalposts/$args['postsbyuser_num']),
						'current'      => isset($_GET['postp']) ? $_GET['postp'] : 1,
						'show_all'     => false,
						'end_size'     => 1,
						'mid_size'     => 2,
						'prev_next'    => true,
						'prev_text'    => __('« Previous','userpro'),
						'next_text'    => __('Next »','userpro'),
						'type'         => 'plain',
						'add_args' => false ,
				));
				$is_paginate = $args['post_paginate'];
				if($is_paginate == 0)
					$args['postsbyuser_num'] = $totalposts;
				$post_query = $userpro->posts_by_user($user_id, $args);
				if (locate_template('userpro/' . $template . '.php') != '') {
					include get_stylesheet_directory() . '/userpro/'. $template . '.php';
				} else {
					include userpro_path . "templates/$template.php";
				}
				wp_reset_query();
				break;
		
			case 'online':
				$users = $userpro->onlineusers();
				if (locate_template('userpro/' . $template . '.php') != '') {
					include get_stylesheet_directory() . '/userpro/'. $template . '.php';
				} else {
					include userpro_path . "templates/$template.php";
				}
				break;
				
			case 'request_verify':
				$userpro->new_verification_request($_POST['up_username']);
				if (locate_template('userpro/' . $template . '.php') != '') {
					include get_stylesheet_directory() . '/userpro/'. $template . '.php';
				} else {
					include userpro_path . "templates/$template.php";
				}
				break;
			
			case 'resend':

				add_action('userpro_pre_form_message', 'userpro_msg_new_secret_key');
				if (locate_template('userpro/' . $template . '.php') != '') {
					include get_stylesheet_directory() . '/userpro/'. $template . '.php';
				} else {
					include userpro_path . "templates/$template.php";
				}
				remove_action('userpro_pre_form_message', 'userpro_msg_new_secret_key');
				break;
				
			case 'reset':
				if( userpro_get_option('enable_reset_by_mail') == 'n' ){
					add_action('userpro_pre_form_message', 'userpro_msg_new_secret_key');
				}
				if (locate_template('userpro/' . $template . '.php') != '') {
					include get_stylesheet_directory() . '/userpro/'. $template . '.php';
				} else {
					include userpro_path . "templates/$template.php";
				}
				remove_action('userpro_pre_form_message', 'userpro_msg_new_secret_key');
				break;
				
			case 'change':
				if (locate_template('userpro/' . $template . '.php') != '') {
					include get_stylesheet_directory() . '/userpro/'. $template . '.php';
				} else {
					include userpro_path . "templates/$template.php";
				}
				break;
				
			case 'delete':
				if (isset($args['user'])){
					$user_id = userpro_get_view_user( $args['user'], 'shortcode_user' );
				} else {
					$user_id = userpro_get_view_user( get_query_var('up_username') );
				}
				if ($user_id == 'not_found' || $user_id == 'not_authorized') {
				
					if (locate_template('userpro/' . $user_id . '.php') != '') {
						include get_stylesheet_directory() . '/userpro/'. $user_id . '.php';
					} else { 
						include userpro_path . "templates/$user_id.php";
					}
					
				} elseif ((int)$user_id) {
				
					if (locate_template('userpro/' . $template . '.php') != '') {
						include get_stylesheet_directory() . '/userpro/'. $template . '.php';
					} else {
						include userpro_path . "templates/$template.php";
					}
					
				} else {
				
					/* attempt to edit profile so force redirect to same page */
					$args['force_redirect_uri'] = 1;
					$template = 'login';$args['template'] = 'login';
					if (locate_template('userpro/' . $template . '.php') != '') {
						include get_stylesheet_directory() . '/userpro/'. $template . '.php';
					} else {
						include userpro_path . "templates/login.php";
					}
					
				}
				break;
		
			case 'memberlist':
				/* Removed from index.php */
				require_once userpro_path . "functions/member-search-filters.php";
				require_once userpro_path . "functions/memberlist-functions.php";
				$users = userpro_memberlist_loop( $args );
				if ($args['memberlist_table'] == 1) {
					$template = 'memberlist_table';
					if (locate_template('userpro/' . $template . '.php') != '') {
						include get_stylesheet_directory() . '/userpro/'. $template . '.php';
					} else {
						include userpro_path . "templates/$template.php";
					}
					
				} elseif ($args['memberlist_v2'] == 0) {
				
					if (locate_template('userpro/' . $template . '.php') != '') {
						include get_stylesheet_directory() . '/userpro/'. $template . '.php';
					} else {
						include userpro_path . "templates/$template.php";
					}
					
				} else {
				
					if (locate_template('userpro/' . $template . '_v2.php') != '') {
						include get_stylesheet_directory() . '/userpro/'. $template . '_v2.php';
					} else {
						include userpro_path . "templates/$template". "_v2.php";
					}
					
				}
				break;
				
			case 'list':
				/* Removed from index.php */
				require_once userpro_path . "functions/member-search-filters.php";
				require_once userpro_path . "functions/memberlist-functions.php";
				$users = userpro_memberlist_listusers($args, $list_users);
				if (locate_template('userpro/' . $template . '.php') != '') {
					include get_stylesheet_directory() . '/userpro/'. $template . '.php';
				} else {
					include userpro_path . "templates/$template.php";
				}
				break;
				
			case 'card':
				if (isset($args['user'])){
					if ($args['user'] == 'author') {
						if (is_author()){
							$user_id = get_query_var('author');
						} else {
							$user_id = get_the_author_meta('ID');
						}
					} else {
						$user_id = userpro_get_view_user( $args['user'], 'shortcode_user' );
					}
				} else {
					if (!$user_id && userpro_is_logged_in()){
						$user_id = get_current_user_id();
					}
					else{
						$user_id = userpro_get_view_user( get_query_var('up_username') );					
					}
				}

				if ($user_id) {
					$get_user = get_userdata($user_id);
					$user = $get_user->user_login;
					if (locate_template('userpro/' . $template . '.php') != '') {
						include get_stylesheet_directory() . '/userpro/'. $template . '.php';
					} else {
						include userpro_path . "templates/$template.php";
					}
				}
				break;

			case 'register':
				if(userpro_get_option('userpro_invite_emails_enable') == 1 && !isset($_GET['code'])) {
					$template = 'registration_closed';$args['template'] = 'registration_closed';
					if (locate_template('userpro/' . $template . '.php') != '') {
						include get_stylesheet_directory() . '/userpro/'. $template . '.php';
					} else {
						include userpro_path . "templates/$template.php";
					}
				}else {
					if(isset($_GET['code'])){
							if (!userpro_is_logged_in() || userpro_is_logged_in() && !userpro_get_option('show_logout_register')  ){
								if (userpro_fields_group_by_template( $template, $args["{$template}_group"] ) != array('') ){
									if (locate_template('userpro/' . $template . '.php') != '') {
										include get_stylesheet_directory() . '/userpro/'. $template . '.php';
									} else {
                                        /**
                                         * Check if invitation code exist
                                         */
                                        $invitationCode = new UP_UserInvitation();
                                        if($invitationCode->getEmailByInvitationCode($_GET['code']))
                                        {
                                            $activation_code = $_GET['code'];
                                            $invitationCode->updateStatus('Not registered');
                                            include userpro_path . "templates/$template.php";
                                        }else{
                                            $template = 'registration_closed';$args['template'] = 'registration_closed';
                                            if (locate_template('userpro/' . $template . '.php') != '') {
                                                include get_stylesheet_directory() . '/userpro/'. $template . '.php';
                                            } else {
                                                include userpro_path . "templates/$template.php";
                                            }
                                        }

									}
								}
							} else {
								$user_id = get_current_user_id();
									if (locate_template('userpro/logout.php') != '') {
										include get_stylesheet_directory() . '/userpro/logout.php';
									} else {
										include userpro_path . "templates/logout.php";
									}
							}
						break;
					}
				
				if ( userpro_get_option('users_can_register') == 0) {
					$template = 'registration_closed';$args['template'] = 'registration_closed';
					if (locate_template('userpro/' . $template . '.php') != '') {
						include get_stylesheet_directory() . '/userpro/'. $template . '.php';
					} else {
						include userpro_path . "templates/$template.php";
					}
				} else {
				
				if (!userpro_is_logged_in() || (userpro_is_logged_in() && !userpro_get_option('show_logout_register') ) ){
					if (userpro_fields_group_by_template( $template, $args["{$template}_group"] ) != array('') ){
						if (locate_template('userpro/' . $template . '.php') != '') {
							include get_stylesheet_directory() . '/userpro/'. $template . '.php';
						} else {
							include userpro_path . "templates/$template.php";
						}
					}
				} else {
					$user_id = get_current_user_id();
					if (locate_template('userpro/logout.php') != '') {
						include get_stylesheet_directory() . '/userpro/logout.php';
					} else {
						include userpro_path . "templates/logout.php";
					}
				}
				
				}
				}
				break;
			
			case 'login':
				if (!userpro_is_logged_in() || (userpro_is_logged_in() && !userpro_get_option('show_logout_login') ) ){
					
					if (userpro_fields_group_by_template( $template, $args["{$template}_group"] ) != array('') ){
						if (locate_template('userpro/' . $template . '.php') != '') {
							include get_stylesheet_directory() . '/userpro/'. $template . '.php';
						} else {
							include userpro_path . "templates/$template.php";
						}
					}
				} else {
					$user_id = get_current_user_id();
					if (locate_template('userpro/logout.php') != '') {
						include get_stylesheet_directory() . '/userpro/logout.php';
					} else {
						include userpro_path . "templates/logout.php";
					}
				}
				break;
				
			case 'edit':
				if (userpro_get_edit_user()){
					$user_id = userpro_get_edit_user();
					if ($user_id == 'not_found' || $user_id == 'not_authorized') {
						if (locate_template('userpro/' . $user_id . '.php') != '') {
							include get_stylesheet_directory() . '/userpro/'. $user_id . '.php';
						} else {
							include userpro_path . "templates/$user_id.php";
						}
					} elseif (userpro_fields_group_by_template( $template, $args["{$template}_group"] ) != array('') ){
						if (locate_template('userpro/' . $template . '.php') != '') {
							include get_stylesheet_directory() . '/userpro/'. $template . '.php';
						} else {
							include userpro_path . "templates/$template.php";
						}
					}
				} else {
				
					/* attempt to edit profile so force redirect to same page */
					$args['force_redirect_uri'] = 1;
					$template = 'login';$args['template'] = 'login';
					if (locate_template('userpro/' . $template . '.php') != '') {
						include get_stylesheet_directory() . '/userpro/'. $template . '.php';
					} else {
						include userpro_path . "templates/login.php";
					}
					
				}
				break;
				
			case 'view':

				if (isset($args['user'])){
					if ($args['user'] == 'author') {
						if (is_author()){
							$user_id = get_query_var('author');
						} else {
							$user_id = get_the_author_meta('ID');
						}
					} else {
						$user_id = userpro_get_view_user( $args['user'], 'shortcode_user' );
					}
				} else {
					$user_id = userpro_get_view_user( get_query_var('up_username') );
				}

				if ($user_id == 'not_found' || $user_id == 'not_authorized') {
					if (locate_template('userpro/' . $user_id . '.php') != '') {
						include get_stylesheet_directory() . '/userpro/'. $user_id . '.php';
					} else {
						include userpro_path . "templates/$user_id.php";
					}
				} elseif ($user_id == 'login_to_view'){
						
					/* attempt to view profile so force redirect to same page */
					//$args['force_redirect_uri'] = 1;
					$template = 'login';
					$args['template'] = 'login';
					if (locate_template('userpro/' . $template . '.php') != '') {
						include get_stylesheet_directory() . '/userpro/'. $template . '.php';
					} else {
						include userpro_path . "templates/login.php";
					}
					
				} elseif ($user_id == 'login_to_view_others'){
						
					/* attempt to view profile so force redirect to same page */
					$args['force_redirect_uri'] = 1;
					$template = 'login';$args['template'] = 'login';
					if (locate_template('userpro/' . $template . '.php') != '') {
						include get_stylesheet_directory() . '/userpro/'. $template . '.php';
					} else {
						include userpro_path . "templates/login.php";
					}
					
				} elseif (userpro_fields_group_by_template( $template, $args["{$template}_group"] ) != array('') ){


					if (locate_template('userpro/' . $template . '.php') != '') {
						include get_stylesheet_directory() . '/userpro/'. $template . '.php';
					} else {
						include userpro_path . "templates/$template.php";
					}
				}
				break;

			case 'connections':
			
			$user_id = get_current_user_id();	
			if (userpro_is_logged_in()){
			
				// Generate user ID
				if (isset($args['user'])){
					if ($args['user'] == 'author') {

						$user_id = get_the_author_meta('ID');
					} else if ($args['user'] == 'loggedin') {
						$user_id = get_current_user_id();

					} else {
						$user_id = userpro_get_view_user( $args['user'], 'shortcode_user' );

					}
				} else {
																


					$user_id = $userpro->try_query_user($user_id);

				}
				

				$user_request = $userpro->connection( $user_id );
				if (locate_template('userpro/' . $template . '.php') != '') {
					include get_stylesheet_directory() . '/userpro/'. $template . '.php';
				} else {
					include userpro_path . "templates/$template.php";
				}
			} else {
			
				/* attempt to view profile so force redirect to same page */
				$args['force_redirect_uri'] = 1;
				$template = 'login';$args['template'] = 'login';
				if (locate_template('userpro/' . $template . '.php') != '') {
					include get_stylesheet_directory() . '/userpro/'. $template . '.php';
				} else {
					include userpro_path . "templates/login.php";
				}
			
			}
			
			$userpro->temp_id = $user_id;
			
		
			//	include userpro_path . "templates/$template.php";
			break;
				
		}
		
		/**
		START THEMING
		**/
		$modern_layout = userpro_get_option('up_modern_layout');
		if( $modern_layout == 0 || ( $modern_layout!==0 && $template != 'view' ) ){
			if ( in_array( $align, array('left','right') ) ) {
				echo '<div class="userpro-clear"></div>';
			}
			if (class_exists('userpro_sk_api') && is_dir( userpro_sk_path . 'skins/'.$skin ) ) {
				wp_register_style('userpro_skin_min', userpro_sk_url . 'skins/'.$skin.'/style.css');
				wp_enqueue_style('userpro_skin_min');
			} else {
				wp_register_style('userpro_skin_min', userpro_url . 'skins/'.$skin.'/style.css');
				wp_enqueue_style('userpro_skin_min');
			}
			if (locate_template('userpro/skins/'.$skin.'/style.css') ) {
				wp_register_style('userpro_skin_custom', get_stylesheet_directory_uri() . '/userpro/skins/'.$skin.'/style.css' );
				wp_enqueue_style('userpro_skin_custom');
			}
		
		}
                
                if( 'reviews' === $template ){
                    $user_id = userpro_get_view_user( get_query_var('up_username') );
                }
		include userpro_path . "css/userpro.php";
		/**
			END THEMING
		**/
		
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
		endif;
	}