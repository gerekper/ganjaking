<?php
function get_publish_page_link() {
$page_id = get_option('userpro_publish_page_link');
$link = trailingslashit ( trailingslashit( get_page_link($page_id) ));
return $link;
}
	add_action( 'save_post', 'userpro_is_publish_shortcode' );
	/* userpro publisher page*/
  function userpro_is_publish_shortcode($post_id){
	$post = get_post($post_id);
	$pattern = get_shortcode_regex();
	preg_match('/'.$pattern.'/s', $post->post_content, $matches);
	if (is_array($matches) && isset($matches[2]) && $matches[2] == 'userpro') {
		if(preg_match('/publish/s', $matches[3])){
			update_option('userpro_publish_page_link', $post_id);
			add_rewrite_rule("$post->post_name/([^/]+)/?",'index.php?page_id='.$post_id.'&up_username=$matches[1]', 'top');
			flush_rewrite_rules();
		}
	}
}



add_action('init', 'userpro_followers_page', 10);

function userpro_followers_page(){


	global $userpro;

	if(!isset($followers) || !isset($following)){
		userpro_set_option('slug_followers', 'followers');
		userpro_set_option('slug_following', 'following');
	}


	$following = userpro_get_option('slug_following');
	$slug_followers = userpro_get_option('slug_followers');


	$slug = userpro_get_option('slug') ;

// followers
	add_rewrite_rule("$slug_followers/([^/]+)/?",'index.php?pagename='.$slug_followers.'&up_username=$matches[1]', 'top');
	add_rewrite_rule("$slug/$slug_followers/([^/]+)/?",'index.php?pagename='.$slug.'/'.$slug_followers.'&up_username=$matches[1]', 'top');
	add_rewrite_rule("$slug/$slug_followers",'index.php?pagename='.$slug.'/'.$slug_followers, 'top' );

//	following
	add_rewrite_rule("$following/([^/]+)/?",'index.php?pagename='.$following.'&up_username=$matches[1]', 'top');
	add_rewrite_rule("$slug/$following/([^/]+)/?",'index.php?pagename='.$slug.'/'.$following.'&up_username=$matches[1]', 'top');
	add_rewrite_rule("$slug/$following",'index.php?pagename='.$slug.'/'.$following, 'top' );

	flush_rewrite_rules();

}


add_action('init', 'userpro_connections_page', 10);

function userpro_connections_page()
{
	global $userpro;
	$pages = get_option('userpro_connections');

	if (!isset($pages['connections'])) {

			$slug_connections = userpro_get_option('slug_connections');


			$connections = array(
				  'post_title'  		=> __('Connections','userpro'),
				  'post_content' 		=> '[userpro template=connections]',
				  'post_name'			=> $slug_connections,
				  'comment_status' 		=> 'closed',
				  'post_type'     		=> 'page',
				  'post_status'   		=> 'publish',
				  'post_author'   		=> 1
			);
			$connections = wp_insert_post($connections);
			$pages['connections'] = $connections;
			$post = get_post($connections, ARRAY_A);
			userpro_set_option('slug_connections', $post['post_name']);


			update_option('userpro_connections', $pages);

			/* Rewrite rules */
			$slug_connections = userpro_get_option('slug_connections');

			add_rewrite_rule("$slug_connections/([^/]+)/?",'index.php?pagename='.$slug_connections.'&up_username=$matches[1]', 'top');



			flush_rewrite_rules();

		} else {

			// pages installed
			$slug_connections = userpro_get_option('slug_connections');


		$slug = userpro_get_option('slug');

		add_rewrite_rule("$slug_connections/([^/]+)/?",'index.php?pagename='.$slug_connections.'&up_username=$matches[1]', 'top');
		add_rewrite_rule("$slug_connections/([^/]+)/?",'index.php?pagename='.$slug.'/'.$slug_connections.'&up_username=$matches[1]', 'top');
		add_rewrite_rule("$slug/$slug_connections",'index.php?pagename='.$slug.'/'.$slug_connections, 'top' );
		flush_rewrite_rules();
		}

}


	add_action('init', 'userpro_first_setup', 10);
	function userpro_first_setup($rebuild=0) {
		global $userpro;
		$pages = get_option('userpro_pages');

		/* Rebuild */
		if ($rebuild) {

			// delete existing pages for userpro
			if (isset($pages) && is_array($pages)){
				foreach( $pages as $page_id ) {
					wp_delete_post( $page_id, true );
				}
			}

			// delete from DB
			delete_option('userpro_pages');

		}


		/* Create pages if they do not exist */
		if (!isset($pages['profile'])) {

			$slug = userpro_get_option('slug');
			$slug_edit = userpro_get_option('slug_edit');
			$slug_register = userpro_get_option('slug_register');
			$slug_login = userpro_get_option('slug_login');
			$slug_directory = userpro_get_option('slug_directory');
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

			$directory_page = array(
				  'post_title'  		=> __('Member Directory','userpro'),
				  'post_content' 		=> '[userpro template=memberlist]',
				  'post_name'			=> $slug_directory,
				  'comment_status' 		=> 'closed',
				  'post_type'     		=> 'page',
				  'post_status'   		=> 'publish',
				  'post_author'   		=> 1,
			);
			$directory_page = wp_insert_post( $directory_page );
			$pages['directory_page'] = $directory_page;
			$post = get_post($directory_page, ARRAY_A);
			userpro_set_option('slug_directory', $post['post_name']);

			$parent = array(
				  'post_title'  		=> __('My Profile','userpro'),
				  'post_content' 		=> '[userpro template=view]',
				  'post_name'			=> $slug,
				  'comment_status' 		=> 'closed',
				  'post_type'     		=> 'page',
				  'post_status'   		=> 'publish',
				  'post_author'   		=> 1,
			);
			$parent = wp_insert_post( $parent );
			$pages['profile'] = $parent;
			$post = get_post($parent, ARRAY_A);
			userpro_set_option('slug', $post['post_name']);

			$edit = array(
				  'post_title'  		=> __('Edit Profile','userpro'),
				  'post_content' 		=> '[userpro template=edit]',
				  'post_name'			=> $slug_edit,
				  'comment_status' 		=> 'closed',
				  'post_type'     		=> 'page',
				  'post_status'   		=> 'publish',
				  'post_author'   		=> 1,
				  'post_parent'			=> $parent
			);
			$edit = wp_insert_post( $edit );
			$pages['edit'] = $edit;
			$post = get_post($edit, ARRAY_A);
			userpro_set_option('slug_edit', $post['post_name']);

			$register = array(
				  'post_title'  		=> __('Register','userpro'),
				  'post_content' 		=> '[userpro template=register]',
				  'post_name'			=> $slug_register,
				  'comment_status' 		=> 'closed',
				  'post_type'     		=> 'page',
				  'post_status'   		=> 'publish',
				  'post_author'   		=> 1,
				  'post_parent'			=> $parent
			);
			$register = wp_insert_post( $register );
			$pages['register'] = $register;
			$post = get_post($register, ARRAY_A);
			userpro_set_option('slug_register', $post['post_name']);

			$login = array(
				  'post_title'  		=> __('Login','userpro'),
				  'post_content' 		=> '[userpro template=login]',
				  'post_name'			=> $slug_login,
				  'comment_status' 		=> 'closed',
				  'post_type'     		=> 'page',
				  'post_status'   		=> 'publish',
				  'post_author'   		=> 1,
				  'post_parent'			=> $parent
			);
			$login = wp_insert_post( $login );
			$pages['login'] = $login;
			$post = get_post($login, ARRAY_A);
			userpro_set_option('slug_login', $post['post_name']);

			update_option('userpro_pages', $pages);

			/* Rewrite rules */
			$slug = userpro_get_option('slug');
			$slug_edit = userpro_get_option('slug_edit');
			$slug_register = userpro_get_option('slug_register');
			$slug_login = userpro_get_option('slug_login');
			$slug_directory = userpro_get_option('slug_directory');
			$slug_logout = userpro_get_option('slug_logout');
			add_rewrite_rule("$slug/$slug_register",'index.php?pagename='.$slug.'/'.$slug_register, 'top');
			add_rewrite_rule("$slug/$slug_login",'index.php?pagename='.$slug.'/'.$slug_login, 'top');
			add_rewrite_rule("$slug/$slug_edit/([^/]+)/?",'index.php?pagename='.$slug.'/'.$slug_edit.'&up_username=$matches[1]', 'top' );
			add_rewrite_rule("$slug/$slug_edit",'index.php?pagename='.$slug.'/'.$slug_edit, 'top' );
			add_rewrite_rule("$slug/([^/]+)/?",'index.php?pagename='.$slug.'&up_username=$matches[1]', 'top');

			flush_rewrite_rules();

		} else {

			// pages installed
			$slug = userpro_get_option('slug');
			$slug_edit = userpro_get_option('slug_edit');
			$slug_register = userpro_get_option('slug_register');
			$slug_login = userpro_get_option('slug_login');
			$slug_directory = userpro_get_option('slug_directory');
			$slug_logout = userpro_get_option('slug_logout');
			add_rewrite_rule("$slug/$slug_register",'index.php?pagename='.$slug.'/'.$slug_register, 'top');
			add_rewrite_rule("$slug/$slug_login",'index.php?pagename='.$slug.'/'.$slug_login, 'top');
			add_rewrite_rule("$slug/$slug_edit/([^/]+)/?",'index.php?pagename='.$slug.'/'.$slug_edit.'&up_username=$matches[1]', 'top' );
			add_rewrite_rule("$slug/$slug_edit",'index.php?pagename='.$slug.'/'.$slug_edit, 'top' );
			add_rewrite_rule("$slug/([^/]+)/?",'index.php?pagename='.$slug.'&up_username=$matches[1]', 'top');





//			Followers
//
			//$slug = userpro_get_option('slug_connections');

			//add_rewrite_rule("$slug_connections/([^/]+)/?",'index.php?pagename='.$slug_connections.'&up_username=$matches[1]', 'top');
			//add_rewrite_rule(" c /$slug_connections/([^/]+)/?",'index.php?pagename='.$slug.'/'.$slug_connections.'&up_username=$matches[1]', 'top');
			//add_rewrite_rule("$slug/$slug_connections",'index.php?pagename='.$slug.'/'.$slug_connections, 'top' );



		}

	}

	/* Setup query variables */
	add_filter( 'query_vars', 'userpro_uid_query_var' );
	function userpro_uid_query_var( $query_vars )
	{
		$query_vars[] = 'up_username';
		//$query_vars[] = 'searchuser';
		return $query_vars;
	}
