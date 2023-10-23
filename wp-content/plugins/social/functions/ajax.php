<?php

	/* Follow user */
	add_action('wp_ajax_nopriv_userpro_sc_follow', 'userpro_sc_follow');
	add_action('wp_ajax_userpro_sc_follow', 'userpro_sc_follow');
	function userpro_sc_follow(){
		global $userpro_social;
		$to = $_POST['to'];
        $from = $_POST['from'];
        $output = '';
		$userpro_social->do_follow($to, $from);

		// User
        $user = new UP_User($to);

		$output = $user->user_social->getFollowActionPlain($from, 'follow');
        $output['count'] = $user->user_social->getUserFollowersCount('followers');

		wp_send_json_success($output);
	}
	
	/* Unfollow user */
	add_action('wp_ajax_nopriv_userpro_sc_unfollow', 'userpro_sc_unfollow');
	add_action('wp_ajax_userpro_sc_unfollow', 'userpro_sc_unfollow');
	function userpro_sc_unfollow(){
		global $userpro_social;
		$to = $_POST['to'];
        $from = $_POST['from'];
		$output = '';
		$userpro_social->do_unfollow($to, $from);

        // User
        $user = new UP_User($to);
        $output = $user->user_social->getFollowActionPlain($from, 'unfollow');
        $output['count'] = $user->user_social->getUserFollowersCount('followers');

        wp_send_json_success($output);
	}
	
	/* refresh activity */
	add_action('wp_ajax_nopriv_userpro_sc_refreshactivity', 'userpro_sc_refreshactivity');
	add_action('wp_ajax_userpro_sc_refreshactivity', 'userpro_sc_refreshactivity');
	function userpro_sc_refreshactivity(){
		global $userpro, $userpro_social;
		
		$output['res'] = '';
		
		if (!$_POST['activity_user'] || $_POST['activity_user'] == 'undefined'){
			$activity_user = null;
		}
                else{
                        $activity_user =  $_POST['activity_user'];
                }
        $user_id = isset($_POST['user_id'])?$_POST['user_id']:null;
        $offset = isset($_POST['offset'])?$_POST['offset']:null;
        $per_page = isset($_POST['per_page'])?$_POST['per_page']:null;      
		$activity = $userpro_social->activity($user_id, $offset, $per_page, $activity_user);
		if (isset($activity) && is_array($activity)):
		foreach($activity as $timestamp=>$status) :
		
		$content = str_replace('{timestamp}', $userpro->time_elapsed( $status['timestamp'] ), $status['status']);
		
		$output['res'] .= '<div class="userpro-sc">';
		
		$output['res'] .= $content;
						
		$output['res'] .= '<div class="userpro-sc-btn">'.$userpro_social->follow_text($status['user_id']).'</div></div>';
		
		endforeach;
		endif;
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* load activity */
	add_action('wp_ajax_nopriv_userpro_sc_loadactivity', 'userpro_sc_loadactivity');
	add_action('wp_ajax_userpro_sc_loadactivity', 'userpro_sc_loadactivity');
	function userpro_sc_loadactivity(){
		global $userpro, $userpro_social;
		
		$output['res'] = '';
		
		if (!$_POST['activity_user'] || $_POST['activity_user'] == 'undefined'){
			$activity_user = null;
		}
                else{
                        $activity_user = $_POST['activity_user'];
                }
        $user_id = isset($_POST['user_id'])?$_POST['user_id']:null;
        $offset = isset($_POST['offset'])?$_POST['offset']:null;
        $per_page = isset($_POST['per_page'])?$_POST['per_page']:null;
		$activity = $userpro_social->activity($user_id, $offset, $per_page, $activity_user);
		if (isset($activity) && is_array($activity)):
		foreach($activity as $timestamp=>$status) :
		
		$content = str_replace('{timestamp}', $userpro->time_elapsed( $status['timestamp'] ), $status['status']);
		
		$output['res'] .= '<div class="userpro-sc">';
		
		$output['res'] .= $content;
						
		$output['res'] .= '<div class="userpro-sc-btn">'.$userpro_social->follow_text($status['user_id']).'</div></div>';
		
		endforeach;
		endif;
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
