<?php

class userpro_dg_api {

	function __construct() {
	}

	/* Loop badges from badges folder */
	function loop_badges(){
		$res = '';
		//delete_option('_userpro_badges');
		$active = null;
		foreach (glob(userpro_dg_path . 'badges/*') as $filename) {
			if (userpro_badges_admin_edit()){
				$info = userpro_badges_admin_edit_info();
				if ( $info['badge_url'] == userpro_dg_url . 'badges/'. basename($filename) ){
					$active = 'active';
				} else {
					$active = null;
				}
			}
			$res .= '<span class="userpro-admin-badge '.$active.'"><img src="'.userpro_dg_url . 'badges/'. basename($filename) .'" alt="" /></span>';
		}
		return $res;
	}

	/* Remove achievement badge */
	function remove_achievement_badge($btype, $bid){
		$badges = get_option('_userpro_badges');
		if (isset( $badges[$btype][$bid] ) ) {
			unset($badges[$btype][$bid]);
			update_option('_userpro_badges',$badges);
		}

		$a_badges = get_option('_userpro_badges_auto');
		if (isset( $a_badges[$btype][$bid] ) ) {
			unset($a_badges[$btype][$bid]);
			update_option('_userpro_badges_auto',$a_badges);
		}

		if( $btype=='defaultbadge'){
			delete_option('userpro_defaultbadge');
		}
	}

	/* Remove user badge */
	function remove_user_badge($user_id, $badge_url) {
		$badges = get_user_meta($user_id, '_userpro_badges', true);
		if (is_array($badges)){
			foreach($badges as $k => $badge){
				if ( $badge['badge_url'] == $badge_url ) {
					unset($badges[$k]);
				}
			}
		}
		update_user_meta($user_id, '_userpro_badges', $badges);
	}

	/* Find manual badges */
	function find_badges( $form ) {
		$result = null;
		unset($form['find-user-badges']);
		$badge_user = $form['badge_user'];
		if ($badge_user == '') {
			$result['error'] = __('You did not choose any user.','userpro');
		}
		return $result;
	}

	/* Add new badge */
	function new_badge( $form ) {
		$result = null;
		unset($form['insert-badge']);
		$badge_method = $form['badge_method'];

		// Manual badge setting
		if ($badge_method == 'manual' || $badge_method == 'manual_roles' || $badge_method == 'auto_roles') {
			if (!$form['badge_url']) {
				$result['error'] = __('You must choose a badge first.','userpro');
			} else if (!$form['badge_title']) {
				$result['error'] = __('You must enter a title for the badge.','userpro');
			} else {

				if (isset($form['badge_to_users']) && is_array($form['badge_to_users'])){
					$this->give_badge_to_users( $form );
					echo '<div class="updated"><p><strong>'.__('Badges have been assigned.','userpro').'</strong></p></div>';
				}
				if(isset($form['badge_to_roles']) && is_array($form['badge_to_roles'])) {
					$this->give_badge_to_roles( $form );
					echo '<div class="updated"><p><strong>'.__('Badges have been assigned.','userpro').'</strong></p></div>';

				}
				if(isset($form['auto_badge_to_roles']) && is_array($form['auto_badge_to_roles'])) {
					$this->give_auto_badge_to_roles( $form );
					echo '<div class="updated"><p><strong>'.__('Badges have been assigned.','userpro').'</strong></p></div>';

				}

			}
		}

		// Achievement
		if ($badge_method == 'achievement') {
			if (!$form['badge_url']) {
				$result['error'] = __('You must choose a badge first.','userpro');
			} else if (!$form['badge_title']) {
				$result['error'] = __('You must enter a title for the badge.','userpro');
			} else if (!$form['badge_achieved_num']) {
				$result['error'] = __('You did not select any number for this achievement.','userpro');
			} else {

				$this->achievement_badge( $form );
				echo '<div class="updated"><p><strong>'.__('Badges have been assigned.','userpro').'</strong></p></div>';

			}
		}
		if($badge_method == 'Defaultbadge')
			{

			$users = get_users( array( 'fields' => array( 'Id' ) ) );
			$setdefaultbadge=array('badge_url' => $form['badge_url'],
					'badge_title' => $form['badge_title'],
					'defaultbadge' => $form['defaultbadge'],
			);
			update_option("userpro_defaultbadge",$setdefaultbadge);

			if($form['defaultbadge']=="1")
			{
			if (!$form['badge_url']) {
				$result['error'] = __('You must choose a badge first.','userpro');
			} else if (!$form['badge_title']) {
				$result['error'] = __('You must enter a title for the badge.','userpro');
			} else {

				if (isset($users) && is_array($users)){
					$this->give_default_badge_to_users( $form,$users);
					echo '<div class="updated"><p><strong>'.__('Badges have been assigned.','userpro').'</strong></p></div>';
				}

			}
		}
		}
		// Points
		if ($badge_method == 'points') {
			if (!$form['badge_url']) {
				$result['error'] = __('You must choose a badge first.','userpro');
			} else if (!$form['badge_title']) {
				$result['error'] = __('You must enter a title for the badge.','userpro');
			} else if (!$form['badge_points_req']) {
				$result['error'] = __('You must enter a required number of points for this badge.','userpro');
			} else {

			}
		}

		return $result;
	}

	/* Achievement badge */
	function achievement_badge($form){
		$achievements = get_option('_userpro_badges');
		if (userpro_badges_admin_edit() && $form['badge_achieved_num'] != $_GET['bid'] ) {
			unset( $achievements[$form['badge_achieved_type']][$_GET['bid']] );
		}
		$achievements[$form['badge_achieved_type']][$form['badge_achieved_num']] = array(
			'badge_url' => $form['badge_url'],
			'badge_title' => $form['badge_title']
		);
		update_option('_userpro_badges', $achievements);
	}

/*Added Yogesh for Default badge to user 8-12-2014
	 */
	function give_default_badge_to_users($form,$users) {

		$result=get_option( 'userpro_defaultbadge' );


		if($result['defaultbadge']=='1')
		{
		foreach($users as $user_id) {

			$badges = get_user_meta($user_id->id, '_userpro_badges', true);
                        if(empty($badges)){
                            $badges = array();
                        }
			// find if that badge exists
			if (is_array($badges)){
				foreach($badges as $k => $badge){
					if ( $badge['badge_default'] == 'yes') {
						unset($badges[$k]);
					}
					if ( $badge['badge_url'] == $form['badge_url'] ) {
						unset($badges[$k]);
					}
					if ( $badge['badge_title'] == $form['badge_title'] ) {
						unset($badges[$k]);
					}

				}
				update_user_meta($user_id->id, '_userpro_badges', true);
			}

			// add new badge to user
			$badges[] = array(
					'badge_url' => $form['badge_url'],
					'badge_title' => $form['badge_title'],
					'badge_default'=>'yes'
			);
			update_user_meta($user_id->id, '_userpro_badges', $badges);
		}
	}}


		/* Give badge to role */
	function give_badge_to_roles($form) {
		foreach($form['badge_to_roles'] as $role) {

			$args = array(
					'role' => $role
			);
			$users = get_users($args);
			foreach ($users as $user) {
			$badges = get_user_meta($user->ID, '_userpro_badges', true);

			// find if that badge exists
			if (is_array($badges)){
				foreach($badges as $k => $badge){
					if ( isset($badge['badge_url']) && $badge['badge_url'] == $form['badge_url'] ) {
						unset($badges[$k]);
					}
					if ( isset($badge['badge_title']) && $badge['badge_title'] == $form['badge_title'] ) {
						unset($badges[$k]);
					}
				}
				update_user_meta($user->ID, '_userpro_badges', true);
			}
			if(empty($badges)){
                            $badges = array();
                        }
			// add new badge to user
			$badges[] = array(
					'badge_url' => $form['badge_url'],
					'badge_title' => $form['badge_title']
			);
			update_user_meta($user->ID, '_userpro_badges', $badges);
		}
		}
		$exitingbadge = get_option('_userpro_badges');

		if(isset($exitingbadge['roles'])){
			if(isset($_GET['bid'])){
				$exitingbadge['roles'][$_GET['bid']] = array('badge_url'=>$form['badge_url'],'badge_title'=>$form['badge_title'],'badge_to_role'=>$form['badge_to_roles']);
			}
			else{
				$exitingbadge['roles'][] = array('badge_url'=>$form['badge_url'],'badge_title'=>$form['badge_title'],'badge_to_role'=>$form['badge_to_roles']);
			}
		}
		else{
			$exitingbadge = array('roles'=>array(array('badge_url'=>$form['badge_url'],'badge_title'=>$form['badge_title'],'badge_to_role'=>$form['badge_to_roles'])));
		}
		update_option('_userpro_badges',$exitingbadge);
	}
	function give_auto_badge_to_roles($form) {

		$exitingbadge = get_option('_userpro_badges_auto');

		if(isset($exitingbadge['auto_roles'])){
			if(isset($_GET['bid'])){
				$exitingbadge['auto_roles'][$_GET['bid']] = array('badge_url'=>$form['badge_url'],'badge_title'=>$form['badge_title'],'auto_badge_to_role'=>$form['auto_badge_to_roles']);
			}
			else{
				$exitingbadge['auto_roles'][] = array('badge_url'=>$form['badge_url'],'badge_title'=>$form['badge_title'],'auto_badge_to_role'=>$form['auto_badge_to_roles']);
			}}
			else{
			$exitingbadge = array('auto_roles'=>array(array('badge_url'=>$form['badge_url'],'badge_title'=>$form['badge_title'],'auto_badge_to_role'=>$form['auto_badge_to_roles'])));

		}

		update_option('_userpro_badges_auto',$exitingbadge);

		if(count($form['auto_badge_to_roles']) > 0){
			foreach($form['auto_badge_to_roles'] as $role){
				$args = array(
					'role' => $role,
				);
				$usersWithRole = get_users($args);

				foreach($usersWithRole as $user){
					addBadgesForUsersAutomatically($user->ID, $form);
				}
			}
		}
	}

	/* Give badge to users */
	function give_badge_to_users($form) {
		foreach($form['badge_to_users'] as $user_id) {

			$badges = get_user_meta($user_id, '_userpro_badges', true);

			// find if that badge exists
			if (is_array($badges)){
				foreach($badges as $k => $badge){
                    if(empty($badge)){
                        unset($badges[$k]);
                        continue;
                    }
					if ( $badge['badge_url'] == $form['badge_url'] ) {
						unset($badges[$k]);
					}
					if ( $badge['badge_title'] == $form['badge_title'] ) {
						unset($badges[$k]);
					}
				}
			}
                // add new badge to user
                if(is_string($badges)){
                    $badges = [];
                }
                $badges[] = array(
                    'badge_url' => $form['badge_url'],
                    'badge_title' => $form['badge_title']
                );

			update_user_meta($user_id, '_userpro_badges', $badges);
		}
	}

	/* Get badges of user */
	function get_badges($user_id){

		// user badges
		$badges = array();
		$badges = get_user_meta($user_id, '_userpro_badges', true);
		// achievements

		$badges_o = get_option('_userpro_badges');
		if (isset($badges_o) && !empty($badges_o)) {
			foreach($badges_o as $t => $n) {
				foreach($n as $k=>$arr){
					if ($t == 'comments') {
						if ($this->count_user_comments($user_id) >= $k ) {

							if (!isset($badges_acquired[$t])) {
								$highest_num = $k;
								$badges_acquired[$t] = $arr;
							} else {
								if ($k > $highest_num) {
									$badges_acquired[$t] = $arr;
									$highest_num = $k;
								}
							}

						}
					}else if($t == 'days')  {
						if($this->get_user_registered_days($user_id) >=$k && $t!='roles'){
							if (!isset($badges_acquired[$t])) {
								$highest_num = $k;
								$badges_acquired[$t] = $arr;
							} else {
								if ($k > $highest_num) {
									$badges_acquired[$t] = $arr;
									$highest_num = $k;
								}
							}
						}
					}
					else if ( $this->count_user_posts($user_id,$t) >= $k && $t!='roles') {

							if (!isset($badges_acquired[$t])) {
								$highest_num = $k;
								$badges_acquired[$t] = $arr;
							} else {
								if ($k > $highest_num) {
									$badges_acquired[$t] = $arr;
									$highest_num = $k;
								}
							}

					}
				}
			}
		}

		// merge and display
		if (isset($badges_acquired)){
			$badges = array_merge( $badges, $badges_acquired );
		}

		// show them
		if (isset($badges) && is_array($badges)){
			return $badges;
		} else {
			return '';
		}

	}

	/* count user posts */
	function count_user_posts($user_id,$type ) {
		$args['author'] = $user_id;
		$args['post_type'] = $type;
		$args['posts_per_page'] = -1;
		$user_posts = new WP_Query($args);
		if (isset($user_posts->posts)){
		return count($user_posts->posts);
		} else {
		return 0;
		}
	}

	/* comment count */
	function count_user_comments($user_id) {
		global $wpdb;
		global $current_user;
		$current_user=wp_get_current_user();
		$count = $wpdb->get_var('
				 SELECT COUNT(comment_ID)
				 FROM ' . $wpdb->comments. '
				 WHERE user_id = "' . $user_id . '"');
		return (int)$count;
	}

	function get_user_registered_days($user_id) {
		$user = get_userdata($user_id);
		$registration_date =  strtotime($user->user_registered);
		$today_date = strtotime('now');
		$datediff = $today_date -$registration_date;
		$duration = floor($datediff/(60*60*24));
		return $duration;
	}
}

$GLOBALS['userpro_badges'] = new userpro_dg_api();
