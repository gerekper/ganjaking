<?php

	/* Get if user is verified */
	function userpro_is_verified($user_id){
		$test = get_user_meta($user_id, 'userpro_verified', true);
		if ($test == 1) {
			return true;
		} else {
			$user = get_userdata($user_id);
			if ( $user->user_level >= 10 ) {
				return true;
			}
		}
		return false;
	}

	/* display badges beside name */
	function userpro_show_badges($user_id, $inline=false, $disallowed=array() ){
		global $current_user, $wp, $userpro, $userpro_vk;

		$current_user=wp_get_current_user();
		$output = null;


		/* verification beside name */
		if ( !in_array( 'verified', $disallowed ) ){
		if (userpro_get_option('verified_badge_by_name') && $userpro->get_verified_status($user_id) == 1 ){
			$output .= userpro_get_badge('verified', '', $tooltip='right');
		}
		}
		
		/* the badges wrap */
		if ($inline == true){
		$output .= '<span class="userpro-badges inline">';
		} else {
		$output .= '<span class="userpro-badges">';
		}
		
		/* Verified */
		if ( !in_array( 'verified', $disallowed ) ){
		if (userpro_is_verified($user_id) && !userpro_get_option('verified_badge_by_name') ) {
			$output .= userpro_get_badge('verified');
		}
		}
		
		/* Show country flag */
		$output .= userpro_get_badge('country',$user_id);
		
		/* Facebook */
		if ($userpro->is_facebook_user($user_id)){
			$output .= userpro_get_badge('facebook');
		}
		
		/* Twitter */
		if ($userpro->is_twitter_user($user_id)){
			$output .= userpro_get_badge('twitter');
		}
		
		/* Google+ */
		if ($userpro->is_google_user($user_id)){
			$output .= userpro_get_badge('google');
		}

		/* Linkedin */
		if ($userpro->is_linkedin_user($user_id)){
			$output .= userpro_get_badge('linkedin');
		}
		/* Instagram */
		if ($userpro->is_instagram_user($user_id)){
			$output .= userpro_get_badge('instagram');
		}

		/* VK integration */
		if (class_exists('userpro_vk_api') && $userpro_vk->is_vk_user($user_id)){
			$output .= $userpro_vk->userpro_get_badge('vk');
		}
		
		/* Envato */
		if ($userpro->is_envato_customer($user_id)){
			$output .= userpro_get_badge('envato');
		}
		
		/* Custom defined badges */
		$badges = apply_filters('userpro_show_badges', $user_id);
		if ($badges != $user_id){
			$output .= $badges;
		}
		
		/* Add custom badges */
		if ( !in_array( 'custom', $disallowed ) ){
			$after_badges = apply_filters('userpro_after_all_badges', $user_id);
			if ( !is_numeric($after_badges)) {
				$output .= $after_badges;
			}
		}
		
		/* Online/offline status */
		if (userpro_get_option('modstate_online')) {
			if ($userpro->is_user_online($user_id)) {
				$user = get_userdata($user_id);
				if(userpro_get_option('hide_online_admin')==1)
				{
					if($user->roles[0]!='administrator')
					$output .= userpro_get_badge('online');
				}
				else 
				{
				$output .= userpro_get_badge('online');
					
				}
			} else {
				if (userpro_get_option('modstate_showoffline')){
					$output .= userpro_get_badge('offline');
				}
			}
		}
		
		$output .= '</span>';
		return $output;
	}
	
	/* show badge */
	function userpro_get_badge($badge,$user_id=null, $tooltip=null) {
		global $userpro;
		switch($badge){
		
			case 'country_big':
				if (get_user_meta($user_id,'country',true)){
					$country = get_user_meta($user_id, 'country', true);
					$country_meta = str_replace(' ','-',$country);
					$country_meta = iconv("utf-8", "ascii//TRANSLIT//IGNORE", $country_meta);
					if (file_exists( userpro_path.'img/flags/'.strtolower($country_meta) . '.png' ) ) {
					return '<img class="userpro-flag-normal userpro-tip-fade" src="'.userpro_url.'img/flags/'.strtolower($country_meta).'.png" alt="" title="'.$country.'" />';
					}
				}
				break;
		
			case 'country':
				if (userpro_get_option('show_flag_in_badges') && get_user_meta($user_id,'country',true)){
					$country = get_user_meta($user_id, 'country', true);
					$country_meta = str_replace(' ','-',str_replace(',','',$country));
					$country_meta = iconv("utf-8", "ascii//TRANSLIT//IGNORE", $country_meta);
					if (file_exists( userpro_path.'img/flags/mini/'.strtolower($country_meta) . '.png' ) ) {
					return '<img class="userpro-profile-badge userpro-profile-badge-'.$badge.' userpro-flag-small" src="'.userpro_url.'img/flags/mini/'.strtolower($country_meta).'.png" alt="" title="'.$country.'" />';
					}
				}
				break;
		
			case 'online':
				return '<img class="userpro-profile-badge userpro-profile-badge-'.$badge.' userpro-hide-from-list" src="'.$userpro->badges_url.'online.png" alt="" title="'.__('User is online :)','userpro').'" />';
				break;
			
			case 'offline':
				return '<img class="userpro-profile-badge userpro-profile-badge-'.$badge.' userpro-hide-from-list" src="'.$userpro->badges_url.'offline.png" alt="" title="'.__('User is offline :(','userpro').'" />';
				break;
		
			case 'verified':
				if ($tooltip == 'right'){
					$class = 'userpro-profile-badge-right userpro-profile-badge-'.$badge;
				} else {
					$class = 'userpro-profile-badge userpro-profile-badge-'.$badge;
				}
				if (userpro_get_option('verified_link')) {
					return '<a href="'.userpro_get_option('verified_link').'"><img class="'.$class.'" src="'.$userpro->badges_url.'badge-verified.png" alt="" title="'.__('Verified Account','userpro').'" /></a>';
				} else {
					return '<img class="'.$class.'" src="'.$userpro->badges_url.'badge-verified.png" alt="" title="'.__('Verified Account','userpro').'" />';
				}
				break;
				
			case 'unverified':
				if (userpro_get_option('verified_link')) {
					return '<a href="'.userpro_get_option('verified_link').'"><img class="userpro-profile-badge userpro-profile-badge-'.$badge.'" src="'.$userpro->badges_url.'badge-unverified.png" alt="" title="'.__('Unverified Account','userpro').'" /></a>';
				} else {
					return '<img class="userpro-profile-badge userpro-profile-badge-'.$badge.'" src="'.$userpro->badges_url.'badge-unverified.png" alt="" title="'.__('Unverified Account','userpro').'" />';
				}
				break;
			case 'blocked':
				if ($tooltip == 'right'){
					$class = 'userpro-profile-badge-right userpro-profile-badge-'.$badge;
				} else {
					$class = 'userpro-profile-badge userpro-profile-badge-'.$badge;
				}
				return '<img class="'.$class.'" src="'.$userpro->badges_url.'blocked.png" alt="" title="'.__('Unblock account','userpro').'" />';
				break;
				
			case 'unblocked':
					return '<img class="userpro-profile-badge userpro-profile-badge-'.$badge.'" src="'.$userpro->badges_url.'unblock.png" alt="" title="'.__('Block account','userpro').'" />';
				break;	
					
			case 'facebook':
				return '<img class="userpro-profile-badge userpro-profile-badge-'.$badge.'" src="'.$userpro->badges_url.'facebook.png" alt="" title="'.__('Facebook Linked','userpro').'" />';
				break;
				
			case 'twitter':
				return '<img class="userpro-profile-badge userpro-profile-badge-'.$badge.'" src="'.$userpro->badges_url.'twitter.png" alt="" title="'.__('Twitter Linked','userpro').'" />';
				break;
				
			case 'google':
				return '<img class="userpro-profile-badge userpro-profile-badge-'.$badge.'" src="'.$userpro->badges_url.'google.png" alt="" title="'.__('Google Linked','userpro').'" />';
				break;

			case 'linkedin':
				return '<img class="userpro-profile-badge userpro-profile-badge-'.$badge.'" src="'.$userpro->badges_url.'linkedin.png" alt="" title="'.__('Linkedin Linked','userpro').'" />';
				break;

			case 'instagram':
				return '<img class="userpro-profile-badge userpro-profile-badge-'.$badge.'" src="'.$userpro->badges_url.'instagram.png" alt="" title="'.__('Linkedin Linked','userpro').'" />';
				break;
				
			case 'envato':
				return '<img class="userpro-profile-badge userpro-profile-badge-'.$badge.'" src="'.$userpro->badges_url.'envato.png" alt="" title="'.__('Verified Customer','userpro').'" />';
				break;
				
		}
	}
