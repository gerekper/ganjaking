<?php

	/* Add the update info on init */
	add_action('init', 'userpro_update_1024', 13);
	function userpro_update_1024(){
		
		if (!userpro_update_installed('1024') && get_option('userpro_pages') ) {
		
			$pages = get_option('userpro_pages');
			if (!isset($pages['logout_page'])) {
			
				$slug_logout = userpro_get_option('slug_logout');
				
				$logout_page = array(
					  'post_title'  		=> __('Logout','userpro'),
					  'post_content' 		=> '',
					  'post_name'			=> $slug_logout,
					  'comment_status' 		=> 'closed',
					  'post_type'     		=> 'page',
					  'post_status'   		=> 'publish',
					  'post_author'   		=> 1,
				);
				$logout_page = wp_insert_post( $logout_page );
				$pages['logout_page'] = $logout_page;
				$post = get_post($logout_page, ARRAY_A);
				userpro_set_option('slug_logout', $post['post_name']);

				update_option('userpro_pages', $pages);
				
				update_option("userpro_update_1024", 1);
				
			}
		
		}
		
	}