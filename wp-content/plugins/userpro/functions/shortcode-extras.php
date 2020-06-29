<?php

	/* external profile in URL */
	add_shortcode('profile_by_url', 'profile_by_url' );
	function profile_by_url( $args=array(), $content=null ) {
		global $wp, $userpro_admin, $userpro;
		ob_start();
		if ( get_query_var('up_username') && !$userpro->viewing_his_profile() ){
			$content = $userpro->content_to_fields($content);
			echo do_shortcode( $content );
		}
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	/* Custom user */
	add_shortcode('custom_user', 'custom_user' );
	function custom_user( $args=array(), $content=null ) {
		global $wp, $userpro_admin, $userpro;
		
		/* arguments */
		$defaults = array(
			'user' => null
		);
		$args = wp_parse_args( $args, $defaults );
			
		ob_start();
		$data = get_user_by('login', $args['user'] );
		$user_id = $data->ID;
		$content = $userpro->content_to_fields($content, $user_id );
		echo do_shortcode( $content );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	/* Registers and display the shortcode */
	add_shortcode('userpro_loggedin', 'userpro_loggedin' );
	function userpro_loggedin( $args=array(), $content=null ) {
		global $wp, $userpro_admin, $userpro;
		ob_start();
		if (userpro_is_logged_in() && !get_query_var('up_username') ){
			$content = $userpro->content_to_fields($content, get_current_user_id() );
			echo do_shortcode( $content );
		}
		$output = ob_get_contents();
		ob_end_clean();
		return $output;

	}
	
	/* Registers and display the shortcode */
	add_shortcode('userpro_loggedout', 'userpro_loggedout' );
	function userpro_loggedout( $args=array(), $content=null ) {
		global $wp, $userpro_admin, $userpro;
		ob_start();
		if (!userpro_is_logged_in()){
			echo do_shortcode( $content );
		}
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
