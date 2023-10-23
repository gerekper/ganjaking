<?php

	/* Hook after name in user list compact */
	add_filter('userpro_content_from_field_filter', 'userpro_sc_content_to_fields', 10, 2);
	function userpro_sc_content_to_fields($content, $user_id){
		global $userpro_social;
		
		$fields['[followers_count]'] = '<span class="up-followers_count">'.$userpro_social->followers_count_plain($user_id).'</span>';
		$fields['[following_count]'] = '<span class="up-following_count">'.$userpro_social->following_count_plain($user_id).'</span>';
		
		$search = array_keys($fields);
		$replace = array_values($fields);
		$content = str_replace( $search, $replace, $content);
		return $content;
	}
	
	/* Filter public activity */
	add_filter('userpro_private_activity_filter', 'userpro_sc_hide_users');
	add_filter('userpro_public_activity_filter', 'userpro_sc_hide_users');
	function userpro_sc_hide_users($array){
		global $userpro;
		foreach($array as $k => $arr){
			if (userpro_sc_get_option('hide_admins')){
				if ($userpro->is_admin( $arr['user_id'] )){
					unset($array[$k]); // hide admin
				}
			}
			if (!$userpro->user_exists( $arr['user_id'] ) ) {
				unset($array[$k]); // hide deleted user
			}
		}
		return $array;
	}