<?php

	/**
	new account
	**/
	add_action('userpro_after_new_registration', 'userpro_sc_new_user');
	function userpro_sc_new_user($user_id){
		global $userpro_social;
		$userpro_social->log_action( 'new_user', $user_id );
	}

	/**
	logged action: new post / update post
	**/
	function userpro_sc_new_post( $new_status, $old_status, $post ) {
		global $userpro_social;
		if(!is_null($userpro_social)){
            $exclude = userpro_sc_get_option('excluded_post_types');
            if ($exclude != ''){
                $exclude_types = explode(',',$exclude);
            } else {
                $exclude_types = array('nav_menu_item');
            }
            if (!in_array($post->post_type, $exclude_types )) {
                // new post
                if ( $new_status == 'publish' && $old_status != 'publish' ) {
                    $user = get_userdata($post->post_author);
                    $userpro_social->log_action( 'new_post', $user->ID, $post->ID, $post->post_title, $post->post_type );
                }
                // updated post
                if ($new_status == 'publish' && $old_status == 'publish' ){
                    $user = get_userdata($post->post_author);
                    $userpro_social->log_action( 'update_post', $user->ID, $post->ID, $post->post_title, $post->post_type );
                }
            }
        }
	}
	add_action('transition_post_status', 'userpro_sc_new_post', 10, 3 );
	
	/**
	logged action: new comment
	**/
	function userpro_sc_new_comment($comment_ID, $comment_status){
		global $userpro_social;
		if ($comment_status == 1) {
			$comment = get_comment($comment_ID, ARRAY_A);
			$post = get_post( $comment['comment_post_ID'] );
			$userpro_social->log_action( 'new_comment', $comment['user_id'], $comment['comment_post_ID'], $post->post_title );
		}
	}
	add_action('comment_post', 'userpro_sc_new_comment',20, 2);
	
	/**
	logged action: new follow
	**/
	function userpro_sc_new_follow( $args ) {
		global $userpro_social;
		$from = $args['from'];
		$to = $args['to'];
		$userpro_social->log_action( 'new_follow', $from, $to );
	}
	add_action('userpro_sc_after_follow', 'userpro_sc_new_follow');
	
	/**
	logged action: stopped follow
	**/
	function userpro_sc_new_unfollow( $args ) {
		global $userpro_social;
		$from = $args['from'];
		$to = $args['to'];
		$userpro_social->log_action( 'stop_follow', $from, $to );
	}
	add_action('userpro_sc_after_unfollow', 'userpro_sc_new_unfollow');
	
	/**
	logged action: user becomes verified
	**/
	function userpro_sc_new_verified_user( $user_id ) {
		global $userpro_social;
		$userpro_social->log_action( 'verified', $user_id );
	}
	add_action('userpro_after_user_verify', 'userpro_sc_new_verified_user');
