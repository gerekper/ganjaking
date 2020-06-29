<?php

	/* Get icon for field */
	function userpro_get_field_icon($key) {
		global $userpro;
		return $userpro->field_icon($key);
	}
	
	/* Show profile links */
	function userpro_profile_icons_noargs( $user_id, $wrapper=null ) {
		global $userpro;
		$res = null;
		foreach( userpro_fields_group_by_template( 'social', 'default' ) as $key => $array ) {
			$icon = $userpro->field_icon($key);
			if ( userpro_profile_data($key, $user_id) && userpro_field_is_viewable_noargs($key, $user_id) && $icon ) {
			$res .= '<a href="'.userpro_link_filter( userpro_profile_data($key, $user_id), $key ).'" class="userpro-profile-icon userpro-tip" title="'.$array['label'].'" target="_blank" ><i class="userpro-icon-'.$icon.'"></i></a>';
			}
		}
		if ($res){
			if ($wrapper){
				echo '<div class="'.$wrapper.'">';
			}
				echo $res;
			if ($wrapper){
				echo '</div>';
			}
		}
	}

	/* Show profile links */
	function userpro_profile_icons( $args, $user_id, $wrapper=null ) {
		global $userpro;
		$res = null;
		foreach( userpro_fields_group_by_template( 'social', $args["social_group"] ) as $key => $array ) {
			$icon = $userpro->field_icon($key);
			if ( userpro_profile_data($key, $user_id) && userpro_field_is_viewable($key, $user_id, $args) && $icon ) {
			$res .= '<a href="'.userpro_link_filter( userpro_profile_data($key, $user_id), $key ).'" class="userpro-profile-icon userpro-tip" title="'.$array['label'].'" target="'.$args['social_target'].'" ><i class="userpro-icon-'.$icon.'"></i></a>';
			}
		}
		if ($res){
			if ($wrapper){
				echo '<div class="'.$wrapper.'">';
			}
				echo $res;
			if ($wrapper){
				echo '</div>';
			}
		}
	}
