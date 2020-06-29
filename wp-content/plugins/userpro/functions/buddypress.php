<?php

	// Override member permalink
	if (userpro_get_option('buddypress_userpro_link_sync')) {
	add_filter( 'bp_get_member_permalink', 'userpro_bp_get_member_permalink', 9999);
	}
	function userpro_bp_get_member_permalink() {
		global $members_template, $userpro;
		return $userpro->permalink( $members_template->member->id );
	}

	// Override profile links
	if (userpro_get_option('buddypress_userpro_link_sync')) {
	add_filter( 'bp_get_displayed_user_link', 'userpro_bp_get_displayed_user_link', 9999 );
	}
	function userpro_bp_get_displayed_user_link() {
		global $userpro;
		return $userpro->permalink( bp_displayed_user_id() );
	}
	
	// Override profile links in activity/misc
	if (userpro_get_option('buddypress_userpro_link_sync')) {
	add_filter('bp_loggedin_user_domain', 'userpro_bp_loggedin_user_domain', 9999);
	}
	function userpro_bp_loggedin_user_domain(){
		global $userpro,$bp;
		if(!is_admin()){
		return $userpro->permalink( bp_loggedin_user_id() );
		}
		else{
			return $bp->loggedin_user->domain;
		}
	}
	
	// Sync member name from buddypress
	if (userpro_get_option('buddypress_userpro_displayname_sync')) {
	add_filter( 'bp_member_name', 'userpro_bp_member_name', 9999);
	}
	function userpro_bp_member_name(){
		global $members_template;
		return userpro_profile_data('display_name', $members_template->member->id);
	}
	
	// Override buddypress first name (use display name)
	if (userpro_get_option('buddypress_userpro_displayname_sync')) {
	add_filter( 'bp_get_user_firstname', 'userpro_bp_get_user_firstname', 9999, 2 );
	}
	function userpro_bp_get_user_firstname($var1, $var2){
		$user_id = bp_displayed_user_id();
		return userpro_profile_data('display_name', $user_id);
	}
	
	// Override buddypress mention name
	if (userpro_get_option('buddypress_userpro_displayname_sync')) {
	add_filter( 'bp_get_displayed_user_mentionname', 'userpro_bp_get_displayed_user_mentionname', 9999 );
	}
	function userpro_bp_get_displayed_user_mentionname() {
		$user_id = bp_displayed_user_id();
		return userpro_profile_data('display_name', $user_id);
	}

	// Override BuddyPress avatar
	if (userpro_get_option('buddypress_userpro_avatar_sync')) {
	add_filter( 'bp_core_fetch_avatar', 'revert_to_default_wp_avatar', 80, 3 );//late load
	}
	function revert_to_default_wp_avatar( $img, $params, $item_id ){
		
			$img_width = 80;
			if( $params['object']!='user' )
				return $img;

			remove_filter( 'bp_core_fetch_avatar', 'revert_to_default_wp_avatar', 80, 3 );

			if( !userpro_user_has_avatar( $item_id ) ){
				$width = $params['width'];
				if ( false !== $width ) {
					$img_width = $width;
				} elseif ( 'thumb' == $params['type'] ) {
					$img_width = bp_core_avatar_thumb_width();
				} else {
					$img_width = bp_core_avatar_full_width();
				}
				$img = get_avatar( $item_id, $img_width );

			}
			else{

                                $img = userpro_get_avatar("", $item_id, $img_width ,$default="");
			
			}

			add_filter( 'bp_core_fetch_avatar', 'revert_to_default_wp_avatar', 80, 3 );
			return $img;
	}

	/**
	* Check if the given user has an uploaded avatar
	* @return boolean
	*/
	function userpro_user_has_avatar( $user_id=false ) {
		// $user_id = bp_loggedin_user_id();
		if ( bp_core_fetch_avatar( array( 'item_id' => $user_id, 'no_grav' => true,'html'=> false ) ) != bp_core_avatar_default() ) {
			   return true;
		 }
		return false;
	}