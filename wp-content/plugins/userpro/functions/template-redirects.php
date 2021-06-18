<?php
	/* Redirect author archive to profile page (optional) */
	add_action( 'template_redirect', 'my_redirect_author_archive' );
	function my_redirect_author_archive() {
		if ( is_author() && userpro_get_option('redirect_author_to_profile') ) {
			global $userpro;
			$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
			wp_safe_redirect( $userpro->permalink( $author->ID ) );
			exit;
		}
	}

	/* Hook into content: Restrict Content */
        function userpro_global_page_restrict(){
            global $userpro, $post;

            if(function_exists( 'is_shop' ) && is_shop()){

                $page_id  = get_option('woocommerce_shop_page_id');

            }else{

            	if(!empty($post))
                $page_id = $post->ID;

            }

            if (isset($page_id)) {


                $go_to = userpro_get_option('restrict_url');

                $is_restricted = get_post_meta( $page_id, '_userpro_edit_restrict', true);

                $roles = get_post_meta($page_id, 'restrict_roles', true);

                if ( $is_restricted && $is_restricted != '' ) {

                    // not logged in - not none

                    if ((isset($page_id) && 

                    $is_restricted != 'none' && 

                    !userpro_is_logged_in() && 

                    get_permalink($page_id) != $go_to && 

                    get_permalink($page_id) != trailingslashit($go_to)) && (( !is_home()) && ( !is_category() ))){

                        wp_safe_redirect( add_query_arg('redirect_to', esc_url(get_permalink($page_id)), esc_url($go_to) ) );

                        exit;
                    }


                    // logged in (page set to verified accounts)

                    if ( isset($page_id) && 

                    $is_restricted == 'verified' && 

                    userpro_is_logged_in() && 

                    !userpro_is_verified( get_current_user_id() ) &&

                    get_permalink($page_id) != $go_to && 

                    get_permalink($page_id) != trailingslashit($go_to) ){

                        wp_safe_redirect( add_query_arg('redirect_to', esc_url(get_permalink($page_id)), esc_url($go_to) ) );
                        exit;
                    }

                    // logged in (page set to specific roles)

                    if ( isset($page_id) && 

                    $is_restricted == 'roles' && 

                    userpro_is_logged_in() && 

                    !$userpro->user_role_in_array( get_current_user_id(), (array)$roles ) &&

                    get_permalink($page_id) != $go_to && 

                    get_permalink($page_id) != trailingslashit($go_to) ){
                        
                        wp_safe_redirect( add_query_arg('redirect_to', esc_url(get_permalink($page_id)), esc_url($go_to) ) );
                        exit;
                    }
                }
            }
        }

    add_action('template_redirect', 'userpro_global_page_restrict');

	/* LOCK ENTIRE SITE for guests */
	function userpro_entire_not_logged_in(){
		global $userpro, $post, $wp_query;
		
		$locked = userpro_get_option('site_guest_lockout');
		$page_id = userpro_get_option('site_guest_lockout_pageid');
		
		$allowed = userpro_get_option('site_guest_lockout_pageids');
		if (!$allowed){
			$allowed = array('');
		} else {
			$allowed = explode(',', $allowed);
		}
		
		if ( $locked && is_numeric($page_id) && !userpro_is_logged_in() ) {
		
			$condition = false;
			$page_data = get_page($page_id);
			if($page_data->post_status == 'publish') $condition = true;
			
			if ($condition == false) return;
			if (isset($post->ID) && $post->ID == $page_id && $condition == true) return;
			if (isset($post->ID) && in_array( $post->ID, $allowed ) && $condition == true) return;
			
			if (isset($post->ID)){
				$redirect_to = get_permalink($post->ID);
			}
			if( isset($wp_query->query) && is_category($wp_query->query_vars['cat'])){
				$redirect_to = get_category_link($wp_query->query_vars['cat']);
			}
			
			if (isset($redirect_to)){
				wp_safe_redirect( add_query_arg('redirect_to', esc_url($redirect_to), esc_url(get_permalink($page_id)) ) );
			} else {
				wp_safe_redirect( get_permalink($page_id) );
			}
			exit;
			
		}
	}
	add_action('template_redirect', 'userpro_entire_not_logged_in');

	/* LOCK homepage only for users */
	function userpro_homepage_logged_in(){
		global $userpro;
		$url = userpro_get_option('homepage_member_lockout');
		if ( !empty($url) && strstr($url, 'http') && is_front_page() && userpro_is_logged_in() ) {
			wp_safe_redirect( $url );
			exit;
		}
	}
	add_action('template_redirect', 'userpro_homepage_logged_in');
	
	/* LOCK homepage only for guests */
	function userpro_homepage_not_logged_in(){
		global $userpro;
		$url = userpro_get_option('homepage_guest_lockout');
		if ( !empty($url) && strstr($url, 'http') && is_front_page() && !userpro_is_logged_in() ) {
			wp_safe_redirect( $url );
			exit;
		}
	}
	add_action('template_redirect', 'userpro_homepage_not_logged_in');
	
	/* Logged in users trying to see login/register */
	function userpro_accessing_login_when_logged(){
		global $userpro;
		if ( ( is_page() || is_single() ) && userpro_is_logged_in() ) {
			global $post;
			$pages = get_option('userpro_pages');
			if ($post->ID == $pages['login'] && userpro_get_option('show_logout_login') ) {
				if (userpro_get_option('after_login') != 'no_redirect'){
					wp_safe_redirect( $userpro->permalink() );
					exit;
				}
			} elseif ($post->ID == $pages['register'] && userpro_get_option('show_logout_register') ) {
				if (userpro_get_option('after_register') != 'no_redirect'){
					wp_safe_redirect( $userpro->permalink() );
					exit;
				}
			}
		}
	}
	add_action('template_redirect', 'userpro_accessing_login_when_logged');
	
	/* Logout page */
	function userpro_logout_page(){
		global $userpro;
		if ( is_page() || is_single() ) {
			global $post;

			$pages = get_option('userpro_pages');

			$post_name = get_the_title( $pages['logout_page'] );

			if ($post->ID == $pages['logout_page'] || $post_name == $post->post_title) {
				if (userpro_is_logged_in()){
				
					$logout = userpro_get_option('logout_uri');
					if ($logout == 1) $url = home_url();
					if ($logout == 2) $url = $userpro->permalink(0, 'login');
					if ($logout == 3) $url = userpro_get_option('logout_uri_custom');
					if (isset($_REQUEST['redirect_to'])){
						$url = $_REQUEST['redirect_to'];
					}
					wp_logout();
					wp_safe_redirect( $url );
					exit;
					
				} else {
				
					wp_safe_redirect( $userpro->permalink(0, 'login') );
					exit;
					
				}
			}
		}
	}
	add_action('template_redirect', 'userpro_logout_page');
