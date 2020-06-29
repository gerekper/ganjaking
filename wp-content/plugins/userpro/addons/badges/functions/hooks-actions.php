<?php


function auto_badge_for_user($user_id){

	$auto_badges = get_user_meta($user_id, '_userpro_badges', true);

	// find if that badge exists

	$user_info = get_userdata($user_id);
	$user_role=implode(' ', $user_info->roles);
	$autobadges=get_option('_userpro_badges_auto');

	if(is_array($autobadges))

		foreach ($autobadges as $badges)
		{

			foreach($badges as $badge)
			{
				if(isset($badge['auto_badge_to_role']) && in_array($user_role,$badge['auto_badge_to_role'])) {
					if (empty($auto_badges['badge_role']) && in_array($user_role, $auto_badges['badge_role'])  ) {
						$auto_badges[] = array(
							'badge_url' => $badge['badge_url'],
							'badge_title' => $badge['badge_title'],
							'badge_role' => $badge['auto_badge_to_role'][0],
						);
						update_user_meta($user_id, '_userpro_badges', $auto_badges);

						break 2;

					} else {

					}
				}

			}

		}
}

function addBadgesForUsersAutomatically($user_id, $form){

	$userBadges = (array)get_user_meta($user_id, '_userpro_badges', true);

	$user_info = get_userdata($user_id);
	$user_role=implode('', $user_info->roles);
	$autobadges=get_option('_userpro_badges_auto');

	$userBadgesUrls = array();
	foreach($userBadges as $userBadge){
		if(isset($userBadge['badge_url']))
			array_push($userBadgesUrls, $userBadge['badge_url']);
	}

	if(is_array($autobadges) && !empty($autobadges)){
		foreach ($autobadges as $badges)
		{
			foreach($badges as $badge)
			{
				if(isset($badge['auto_badge_to_role']) && in_array($user_role,$badge['auto_badge_to_role']) && !in_array($form['badge_url'], $userBadgesUrls)) {
					$userBadges[] = array(
						'badge_url' => $form['badge_url'],
						'badge_title' => $form['badge_title'],
					);

					break 2;
				}

			}

		}
	}
	update_user_meta($user_id, '_userpro_badges', $userBadges);
}

add_action('userpro_after_new_registration','add_auto_badge');

function add_auto_badge($user_id)
	{
		$auto_badges = get_user_meta($user_id, '_userpro_badges', false);
			
			// find if that badge exists
		
		$user_info = get_userdata($user_id);
		$user_role=implode(' ', $user_info->roles);
		$autobadges=get_option('_userpro_badges_auto');
		$userBadges = array();
		
		if(is_array($autobadges))
		foreach ($autobadges as $badges)
		{
			foreach($badges as $badge) 
			{
				if(isset($badge['auto_badge_to_role']) && in_array($user_role,$badge['auto_badge_to_role']))
				{
					if (is_array($auto_badges)){
						foreach($auto_badges as $k => $auto_badge){
							if ( $auto_badges['badge_url'] == $form['badge_url'] ) {
								unset($auto_badges[$k]);
							}
							if ( $auto_badges['badge_title'] == $form['badge_title'] ) {
								unset($auto_badges[$k]);
							}
						}

						update_user_meta($user_id, '_userpro_badges', true);
					}

					$userBadges[] = array(
						'badge_url' => $badge['badge_url'],
						'badge_title' => $badge['badge_title'],
					);
				}	
			}		
		}
		update_user_meta($user_id, '_userpro_badges', $userBadges);
	}

	
	/* add custom badges */
	add_filter('userpro_show_badges', 'userpro_badges_show');
	function userpro_badges_show($user_id){
		global $userpro_badges;
		$output = null;

		/* Find user badges (get_user_meta - _userpro_badges) */
		$get_badges = $userpro_badges->get_badges($user_id);

		if (is_array($get_badges)){
			foreach($get_badges as $key => $badge) {
				if (isset($badge['badge_url'])) {
					$sanitized = preg_replace('/\s*/', '', $badge['badge_title'] );
					$sanitized = strtolower($sanitized);
					$output .= '<img class="userpro-profile-badge userpro-profile-badge-'.$sanitized.'" src="'.$badge['badge_url'].'" alt="" title="'.$badge['badge_title'].'" />';
				}
			}
		}
		
		return $output;
	
	}
add_action('userpro_after_new_registration', "default_badge_for_registration");
	function default_badge_for_registration($user_id)
	{
		$user_info = get_userdata($user_id);
		$user_role=implode(', ', $user_info->roles);
		$autobadges=get_option('_userpro_badges_auto');

		$result=get_option( 'userpro_defaultbadge' );
		if($result['defaultbadge']=='1')
		{
		$badges = get_user_meta($user_id, '_userpro_badges', true);
		
		// find if that badge exists
		if (is_array($badges)){
			foreach($badges as $k => $badge){
				if ( $badge['badge_url'] == $badge_url ) {
					unset($badges[$k]);
				}
				if ( $badge['badge_title'] == $badge_title ) {
					unset($badges[$k]);
				}
			}
			update_user_meta($user_id, '_userpro_badges', true);
		}
		
		// add new badge to user
		$badges[] = array(
				'badge_url' => $result['badge_url'],
				'badge_title' => $result['badge_title'],
				'badge_default'=>'yes'

		);
		update_user_meta($user_id, '_userpro_badges', $badges);
		
		}
	}
