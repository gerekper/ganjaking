<?php

	/* Registers and display the shortcode */
	add_shortcode('userpro_private', 'userpro_private' );
	function userpro_private( $args=array(), $content=null ) {
		global $post, $wp, $userpro_admin, $userpro;
		
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
		/* Deafult Arguments */
		$default_args = array(
					
				'template' 							=> null,
				'max_width'							=> userpro_get_option('width'),
				'uploads_dir'						=> $userpro->get_uploads_url(),
				'default_avatar_male'				=> userpro_url . 'img/default_avatar_male.jpg',
				'default_avatar_female'				=> userpro_url . 'img/default_avatar_female.jpg',
				'layout'							=> userpro_get_option('layout'),
				'margin_top'						=> 0,
				'margin_bottom'						=> '30px',
				'align'								=> 'center',
				'skin'								=> userpro_get_option('skin'),
				'required_text'						=> __('This field is required','userpro'),
				'password_too_short'				=> __('Your password is too short','userpro'),
				'passwords_do_not_match'			=> __('Passwords do not match','userpro'),
				'password_not_strong'				=> __('Password is not strong enough','userpro'),
				'keep_one_section_open'				=> 0,
				'allow_sections'					=> 1,
				'permalink'							=> $permalink,
				'field_icons'						=> userpro_get_option('field_icons'),
				'profile_thumb_size'				=> 80,
					
				'register_heading' 					=> __('Register an Account','userpro'),
				'register_side'						=> __('Already a member?','userpro'),
				'register_side_action'				=> 'login',
				'register_button_action'			=> 'login',
				'register_button_primary'			=> __('Register','userpro'),
				'register_button_secondary'			=> __('Login','userpro'),
				'register_group'					=> 'default',
				'register_redirect'					=> '',
				'type'								=> userpro_mu_get_option('multi_forms_default'),
					
				'login_heading' 					=> __('Login','userpro'),
				'login_side'						=> __('Forgot your password?','userpro'),
				'login_side_action'					=> 'reset',
				'login_button_action'				=> 'register',
				'login_button_primary'				=> __('Login','userpro'),
				'login_button_secondary'			=> __('Create an Account','userpro'),
				'login_group'						=> 'default',
				'login_redirect'					=> '',
					
				'delete_heading'					=> __('Delete Profile','userpro'),
				'delete_side'						=> __('Undo, back to profile','userpro'),
				'delete_side_action'				=> 'view',
				'delete_button_action'				=> 'view',
				'delete_button_primary'				=> __('Confirm Deletion','userpro'),
				'delete_button_secondary'			=> __('Back to Profile','userpro'),
				'delete_group'						=> 'default',
					
				'reset_heading'						=> __('Reset Password','userpro'),
				'reset_side'						=> __('Back to Login','userpro'),
				'reset_side_action'					=> 'login',
				'reset_button_action'				=> 'change',
				'reset_button_primary'				=> __('Request Secret Key','userpro'),
				'reset_button_secondary'			=> __('Change your Password','userpro'),
				'reset_group'						=> 'default',
					
				'change_heading'					=> __('Change your Password','userpro'),
				'change_side'						=> __('Request New Key','userpro'),
				'change_side_action'				=> 'reset',
				'change_button_action'				=> 'reset',
				'change_button_primary'				=> __('Change my Password','userpro'),
				'change_button_secondary'			=> __('Do not have a secret key?','userpro'),
				'change_group'						=> 'default',
					
				'list_heading'						=> __('Latest Members','userpro'),
				'list_per_page'						=> 5,
				'list_sortby'						=> 'registered',
				'list_order'						=> 'desc',
				'list_users'						=> '',
				'list_group'						=> 'default',
				'list_thumb'						=> 50,
				'list_showthumb'					=> 1,
				'list_showsocial'					=> 1,
				'list_showbio'						=> 0,
				'list_verified'						=> 0,
				'list_relation'						=> 'or',
				'list_popup_view'				    => 0,
				'online_heading'					=> __('Who is online now','userpro'),
				'online_thumb'						=> 30,
				'online_showthumb'					=> 1,
				'online_showsocial'					=> 0,
				'online_showbio'					=> 0,
				'online_mini'						=> 1,
				'online_mode'						=> 'vertical',
					
				'edit_button_primary'				=> __('Save Changes','userpro'),
				'edit_group'						=> 'default',
					
				'view_group'						=> 'default',
					
				'social_target'						=> '_blank',
				'social_group'						=> 'default',
					
				'link_target'						=> '_blank',
					
				'error_heading'						=> __('An error has occured','userpro'),
					
				'memberlist_v2'						=> 1,
				'memberlist_v2_pic_size'			=> '86',
				'memberlist_v2_fields'				=> 'age,gender,country',
				'memberlist_v2_bio'					=> 1,
				'memberlist_v2_showbadges'			=> 1,
				'memberlist_v2_showname'			=> 1,
				'memberlist_v2_showsocial'			=> 1,
					
				'memberlist_pic_size'				=> '120',
				'memberlist_pic_topspace'			=> '15',
				'memberlist_pic_sidespace'			=> '30',
				'memberlist_pic_rounded'			=> 1,
				'memberlist_width'					=> '100%',
				'memberlist_paginate'				=> 1,
				'memberlist_paginate_top'			=> 1,
				'memberlist_paginate_bottom' 		=> 1,
				'memberlist_show_name'				=> 1,
				'memberlist_popup_view'				=> 0,
				'memberlist_withavatar'				=> 0,
				'memberlist_verified'				=> 0,
				'memberlist_filters'				=> '',
				'memberlist_default_search'			=> 1,
				'per_page'							=> 12,
				'sortby'							=> 'registered',
				'order'								=> 'desc',
				'relation'							=> 'and',
				'search'							=> 1,
					
				'show_social'						=> 1,
					
				'registration_closed_side'			=> __('Existing member? login','userpro'),
				'registration_closed_side_action'	=> 'login',
					
				'facebook_redirect'					=> 'profile',
					
				'logout_redirect'					=> '',
					
				'postsbyuser_num'					=> '12',
				'postsbyuser_types'					=> 'post',
				'postsbyuser_mode'					=> 'grid',
				'postsbyuser_thumb'					=> 50,
				'postsbyuser_showthumb'				=> 1,
					
				'publish_heading'					=> __('Add a New Post','userpro'),
				'publish_button_primary'			=> __('Publish','userpro'),
				'publish_button_draft'				=> __('Save as Draft','userpro'),
					
				'restrict_to_verified'				=> 0,
				'restrict_to_roles'					=> '',
		);
		$defaults = apply_filters('userpro_shortcode_args', $default_args );
		$args = wp_parse_args( $args, $defaults );
		/* The arguments are passed via shortcode through admin panel*/
		foreach ($default_args as $key => $val) {
			if(isset($args[$key])) {
				$$key = $args[$key];
			}else {
				$$key = $val;
			}
		}
		
		STATIC $i = 0;
	
		ob_start();

		/* increment wall */
		$i = rand(1, 1000);
		
		/* show the buffer */
		if ($userpro->can_view_private_content( $restrict_to_verified, $restrict_to_roles ) === '1') {
		
			echo do_shortcode( $content );
			
		} elseif ($userpro->can_view_private_content( $restrict_to_verified, $restrict_to_roles ) === '-2') {

			$template = 'restricted'; $args['template'] = 'restricted';
			if (locate_template('userpro/' . $template . '.php') != '') {
				include get_stylesheet_directory() . '/userpro/'. $template . '.php';
			} else {
				include userpro_path . "templates/restricted.php";
			}
		
		} else {
		
			$template = 'private'; $args['template'] = 'private';
			if (locate_template('userpro/' . $template . '.php') != '') {
				include get_stylesheet_directory() . '/userpro/'. $template . '.php';
			} else {
				include userpro_path . "templates/private.php";
			}
			
		}
		
		/**
		START THEMING
		**/
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
		
		include userpro_path . "css/userpro.php";
		/**
			END THEMING
		**/
				
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;

	}
