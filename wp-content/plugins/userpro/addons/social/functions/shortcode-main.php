<?php

	/* Filter shortcodes args */
	add_filter('userpro_shortcode_args', 'userpro_sc_shortcodes_arg', 99);
	function userpro_sc_shortcodes_arg($args){
		$args['following_heading'] = __('Following','userpro');
		$args['followers_heading'] = __('Followers','userpro');
		$args['activity_heading'] = __('Recent Activity','userpro');
		$args['activity_all'] = 0;
		$args['activity_per_page'] = userpro_sc_get_option('activity_per_page');
		$args['activity_side'] = 'refresh';
		$args['activity_user'] = '';
		return $args;
	}

	/* Add extension shortcodes */
	add_action('userpro_custom_template_hook', 'userpro_sc_shortcodes', 99 );
	function userpro_sc_shortcodes($args) {
		global $userpro, $userpro_social;
	    $userpro->up_enqueue_scripts_styles();
		$template = $args['template'];
		
		if (!userpro_get_option('modstate_social') ) return false;

		$query_id = userpro_get_view_user( get_query_var('up_username') );
		$user_id = get_current_user_id();	
		// show activity
		if (isset($args['template']) && $args['template'] == 'activity') {
		
			// ALL ACTIVITY
			if ($args['activity_all'] == 1) {
			
				// ACTIVITY OPEN TO ALL
				if (userpro_sc_get_option('activity_open_to_all') == 1) {
					$activity = $userpro_social->activity(0, 0, $args['activity_per_page'], $args['activity_user'] );
					if (locate_template('userpro/' . $template . '.php') != '') {
						include get_stylesheet_directory() . '/userpro/'. $template . '.php';
					} else {
						include userpro_sc_path . "templates/$template.php";
					}
				} else {
					
					if (userpro_is_logged_in()){
						$activity = $userpro_social->activity(0, 0, $args['activity_per_page'], $args['activity_user'] );
						if (locate_template('userpro/' . $template . '.php') != '') {
							include get_stylesheet_directory() . '/userpro/'. $template . '.php';
						} else {
							include userpro_sc_path . "templates/$template.php";
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
					
				}
			
			// FOLLOWED ACTIVITY
			} 
			else if(isset($args['activity_user']) && $args['activity_user'] == 'self' && $query_id==$user_id){
				if ( userpro_is_logged_in() ){
					$activity = $userpro_social->activity($user_id, 0, $args['activity_per_page'], $args['activity_user'] );
					if (locate_template('userpro/' . $template . '.php') != '') {
						include get_stylesheet_directory() . '/userpro/'. $template . '.php';
					} else {
						include userpro_sc_path . "templates/$template.php";
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
			}
			else {
			
				if ( userpro_is_logged_in() ){
					if ( $user_id == $query_id ) {
					$activity = $userpro_social->activity( $user_id, 0, $args['activity_per_page'], $args['activity_user'] );
					if (locate_template('userpro/' . $template . '.php') != '') {
						include get_stylesheet_directory() . '/userpro/'. $template . '.php';
					} else {
						include userpro_sc_path . "templates/$template.php";
					}
					} else {
						// show nothing
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
			
			}
			
		}
		
		// show users I am following
		if (isset($args['template']) && $args['template'] == 'following') {
		
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
				
				$following = $userpro_social->following( $user_id );
				if (locate_template('userpro/' . $template . '.php') != '') {
					include get_stylesheet_directory() . '/userpro/'. $template . '.php';
				} else {
					include userpro_sc_path . "templates/$template.php";
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
			
		}
		
		// show followers
		if (isset($args['template']) && $args['template'] == 'followers') {
		
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
				
				$followers = $userpro_social->followers( $user_id );
				if (locate_template('userpro/' . $template . '.php') != '') {
					include get_stylesheet_directory() . '/userpro/'. $template . '.php';
				} else {
					include userpro_sc_path . "templates/$template.php";
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
			
		}
		
	}
