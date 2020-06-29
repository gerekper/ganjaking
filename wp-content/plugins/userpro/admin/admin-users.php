<?php

add_filter('manage_users_columns', 'userpro_admin_users_badges_add');
function userpro_admin_users_badges_add($columns) {
	if (userpro_get_option('backend_users_change')){
	unset($columns['username']);
	$columns = array_slice($columns, 0, 1, true) +
		array("userpro_username" => __('Username','userpro') ) +
		array_slice($columns, 1, count($columns)-1, true);
	}
    $columns['userpro_admin_badges'] = __('Badges','userpro');
	$columns['userpro_verify'] = __('Verified','userpro');
	$columns['block_users'] = __('Block user','userpro');
    return $columns;
}
 
add_action('manage_users_custom_column',  'userpro_admin_users_badges', 10, 3);
function userpro_admin_users_badges($value, $column_name, $user_id) {
	global $userpro, $userpro_admin;
	$userpro_admin->add_admin_scripts();
	$userpro_admin->add_admin_styles();
    $user = get_userdata( $user_id );
	
	if (userpro_get_option('backend_users_change')){
	if ( 'userpro_username' == $column_name) {
		$res = '<div class="upadmin-avatar">'.get_avatar($user_id, 40).'</div>';
		$res .= '<strong><a href="'.$userpro->permalink($user_id).'" target="_blank" title="'.__('View Profile','userpro').'">'.$user->user_login.'</a></strong><br />';
		$res .= '<span class="upadmin-small-name">('.userpro_profile_data('display_name', $user_id).')</span>';
		$res .= '<div class="row-actions"><span class="edit"><a href="'.$userpro->permalink($user_id, 'edit').'" target="_blank">'.__('Edit Profile','userpro').'</a></span></div>';
		return $res;
	}
	}
	
	if ( 'userpro_admin_badges' == $column_name ) {
		$res = userpro_show_badges($user_id, true);
		return $res;
	}
	
	if ( 'userpro_verify' == $column_name ) {
		$res = '<div class="upadmin-verify-v2">';
		if ($userpro->get_verified_status($user_id) == 0){
		$res .= '<a href="#" class="button upadmin-verify-u" data-user="'.$user_id.'">'.userpro_get_badge('unverified').'</a>';
		} else {
			if (userpro_is_admin($user_id)) {
				$res .= '<a href="#" class="button button-primary upadmin-unverify-u" data-user="'.$user_id.'">'.userpro_get_badge('verified').'</a>';
			} else {
				$res .= '<a href="#" class="button button-primary upadmin-unverify-u" data-user="'.$user_id.'">'.userpro_get_badge('verified').'</a>';
			}
		}
		if ($userpro->get_verified_status($user_id) == 0){
			if ($userpro->invited_to_verify($user_id)){
				$res .= '&nbsp;&nbsp;' . __('Invitation sent!','userpro');
			} else {
				$res .= '<a href="#" class="button upadmin-invite-u" data-user="'.$user_id.'">'.__('Verified Invite','userpro').'</a>';
			}
		}
		$res .= '</div>';
		return $res;
	}
	if( 'block_users' == $column_name){
		$res = '<div class="upadmin-block-v2">';
		if ($userpro->get_account_status($user_id) == 0){
			$res .= '<a href="#" class="button upadmin-block-u" data-user="'.$user_id.'">'.userpro_get_badge('unblocked').'</a>';
		} else {
			if (userpro_is_admin($user_id)) {
				$res .= '<a href="#" class="button upadmin-unblock-u" data-user="'.$user_id.'">'.userpro_get_badge('blocked').'</a>';
			} else {
				$res .= '<a href="#" class="button upadmin-unblock-u" data-user="'.$user_id.'">'.userpro_get_badge('blocked').'</a>';
			}
		}
		if ($userpro->get_account_status($user_id) == 1){
				$res .= '<span class="button" data-user="'.$user_id.'">'.__('Account Blocked','userpro').'</span>';
		}
		$res .= '</div>';
		
		$res .= '</div>';
		return $res;
	}
    return $value;
}
