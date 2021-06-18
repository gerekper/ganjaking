<?php

add_action('wp_ajax_nopriv_userpro_msg_connection', 'userpro_msg_connection');
add_action('wp_ajax_userpro_msg_connection', 'userpro_msg_connection');

function userpro_msg_connection(){
	
	global $userpro, $userpro_msg , $wpdb;
	$output = '';
	$user_id=get_current_user_id();
	$connected_user_list = get_user_meta($user_id, '_userpro_connected_userlist', true);
	$rolethat_can_receive=userpro_msg_get_option('roles_that_can_recive_message');
	$rolethat_can_receive=explode(',',$rolethat_can_receive);
	
	$connection_msg_body = $_POST['connection_msg_body'];

	if ( !userpro_is_logged_in() || $user_id != get_current_user_id() ) die();
	
	$no_of_users = '';
	$sent_count = 0;
	$blog_id = get_current_blog_id();
	if(!empty($connected_user_list)){
		foreach($connected_user_list as $key => $value){
			$registered_users[] = $key;
		}
		$no_of_users = count($registered_users);
	}
	if($no_of_users>0){
		for($i=0;$i<$no_of_users;$i++){
			if($registered_users[$i]!=$user_id)
			{
				$user = new WP_User(  $registered_users[$i] );
				if($user_id != 0 && !empty($user)){
					$user_roles = $user->roles;
					if(isset($user_roles)){
						$chat_with_userrole= $user_roles[0];
					}
				}
				if( (isset($rolethat_can_receive) && in_array($chat_with_userrole,$rolethat_can_receive)) || empty($rolethat_can_receive[0]) ){
					
					$userpro_msg->do_chat_dir( $registered_users[$i], $user_id, $mode='inbox' );
					$userpro_msg->write_chat( $registered_users[$i], $user_id, $connection_msg_body, $mode='inbox' );
					$sent_count++;
					$message = 'Message sent to '.$sent_count.' out of '.($no_of_users).' users.';
				}
			}
			else
			{
				$message = "Sending message to connections...";
			}
		}
		echo "Your message has been successfully sent to connections.";
	}
	else{
		echo 'No connections found to send the message.';
	}
	die();
}

add_action('wp_ajax_nopriv_up_connection_msg', 'up_connection_msg');
add_action('wp_ajax_up_connection_msg', 'up_connection_msg');

function up_connection_msg()
{
	global $userpro, $userpro_msg;
	$output = array();
	$user_id = $_POST['user_id'];
	if ( !userpro_is_logged_in() || $user_id != get_current_user_id() ) die();
	
	ob_start();
	
	require_once userpro_msg_path . 'templates/connections.php';
	
	$output['html'] = ob_get_contents();
	
	ob_end_clean();
	
	$output=json_encode($output);
		
	if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	
}

add_action('wp_ajax_nopriv_userpro_unblock_user', 'userpro_unblock_user');
add_action('wp_ajax_userpro_unblock_user', 'userpro_unblock_user');

	function userpro_unblock_user()
	{
		$blockuserlist=(array)get_user_meta(get_current_user_id(),"_userpro_messaging_block_users_list");
		$userkey = array_search($_POST['user_id'], $blockuserlist);
		unset($blockuserlist[$userkey]);
		update_user_meta(get_current_user_id(),"_userpro_messaging_block_users_list",$blockuserlist);
		$list = (array)get_user_meta($_POST['user_id'],"_userpro_messaging_block_users");
		$key = array_search($_POST['user_id'], $list);
		unset($list[$key]);
		update_user_meta($_POST['user_id'],"_userpro_messaging_block_users",$list);
		
		
		$res = '<img data-chat_with="'.$_POST['user_id'].'" data-chat_from="'.get_current_user_id().'" class="userpro-profile-badge userpro-profile-badge-msg userpro-init-chat" src="'.userpro_msg_url . 'img/icon-chat-small.png" alt="" title="'.__('Send a message','userpro-msg').'" />';
		
		echo $res;die();
	}

       /*Block Users added Yogesh */
		
	add_action('wp_ajax_nopriv_userpro_block_user', 'userpro_block_user');
	add_action('wp_ajax_userpro_block_user', 'userpro_block_user');
	function userpro_block_user()
	{	global $userpro, $userpro_msg;	
		$user_id=$_POST['block_user'];
		$blockto=$_POST['user'];
		$blockusers=get_user_meta($blockto,"_userpro_messaging_block_users");
		$blockuserlist=get_user_meta($user_id,"_userpro_messaging_block_users_list");
		
		if(is_array($blockuserlist))	
		{
			$blockuserlist[]=$blockto;
		}
		else
		{
			$blockuserlist=$blockto;
		}
		update_user_meta($user_id,"_userpro_messaging_block_users_list",$blockuserlist); 
        if(is_array($blockusers))
		{
			$blockusers[] =$user_id;
		}
		else
		{
			$blockusers=$user_id;
		}
		$blockuserslist=get_user_meta($blockto,"_userpro_messaging_block_users",true);
		if(!in_array($user_id,$blockuserslist))
		update_user_meta($blockto,"_userpro_messaging_block_users",$blockusers); 
		
		if ( !userpro_is_logged_in() || $user_id != get_current_user_id() ) die();
		$userpro_msg->remove_unread_chat($user_id,$blockto);
		$userpro_msg->remove_read_chat($blockto,$user_id);
		$userpro_msg->remove_unread_chat($blockto,$user_id);
		$userpro_msg->remove_read_chat($user_id,$blockto);
		
		
	}

	/* delete a chat */
	add_action('wp_ajax_nopriv_userpro_delete_conversation', 'userpro_delete_conversation');
	add_action('wp_ajax_userpro_delete_conversation', 'userpro_delete_conversation');
	function userpro_delete_conversation(){
		global $userpro, $userpro_msg;
		$output = '';

		$chat_from = $_POST['chat_from'];
		$chat_with = $_POST['chat_with'];		
		if ( !userpro_is_logged_in() || $chat_from != get_current_user_id() ) die();
		if (!$userpro_msg->can_chat_with( $chat_with )) die();
		
		$userpro_msg->remove_unread_chat($chat_from, $chat_with);
		$userpro_msg->remove_read_chat($chat_from, $chat_with);
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}

	/* init a chat */
	add_action('wp_ajax_nopriv_userpro_init_chat', 'userpro_init_chat');
	add_action('wp_ajax_userpro_init_chat', 'userpro_init_chat');
	function userpro_init_chat(){
		global $userpro, $userpro_msg;
		$output = array();
		
		$chat_from = $_POST['chat_from'];
		$chat_with = $_POST['chat_with'];
		if ( !userpro_is_logged_in() || $chat_from != get_current_user_id() ) die();
		if (!$userpro_msg->can_chat_with( $chat_with )) die();
		
		ob_start();
		
		require_once userpro_msg_path . 'templates/new-message.php';

		$output['html'] = ob_get_contents();
		
		ob_end_clean();
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* show conversation */
	add_action('wp_ajax_nopriv_userpro_view_conversation', 'userpro_view_conversation');
	add_action('wp_ajax_userpro_view_conversation', 'userpro_view_conversation');
	function userpro_view_conversation(){
		global $userpro, $userpro_msg;
		$output = array();
		$chat_from = $_POST['chat_from'];
		$chat_with = $_POST['chat_with'];
		if ( !userpro_is_logged_in() || $chat_from != get_current_user_id() ) die();
		
		$userpro_msg->remove_unread_chat($chat_from, $chat_with);
			
		ob_start();
		require_once userpro_msg_path . 'templates/conversation.php';
		$output['html'] = ob_get_contents();
		ob_end_clean();
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* show chat */
	add_action('wp_ajax_nopriv_userpro_show_chat', 'userpro_show_chat');
	add_action('wp_ajax_userpro_show_chat', 'userpro_show_chat');
	function userpro_show_chat(){
		global $userpro, $userpro_msg;
		$output = array();
		$user_id = $_POST['user_id'];		
		if ( !userpro_is_logged_in() || $user_id != get_current_user_id() ) die();
		
		ob_start();
		
		require_once userpro_msg_path . 'templates/messages.php';

		$output['html'] = ob_get_contents();
		
		ob_end_clean();
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* start a chat */
	add_action('wp_ajax_nopriv_userpro_start_chat', 'userpro_start_chat');
	add_action('wp_ajax_userpro_start_chat', 'userpro_start_chat');
	function userpro_start_chat(){
		global $userpro, $userpro_msg;
		$output = array();
		$chat_from = $_POST['chat_from'];
		$chat_with = $_POST['chat_with'];
		$chat_body = $_POST['chat_body'];
		if ( !userpro_is_logged_in() || $chat_from != get_current_user_id() ) die();
		if (!$userpro_msg->can_chat_with( $chat_with )) die();
		
		
		/* Create folders to store conversations */
		$userpro_msg->do_chat_dir( $chat_from, $chat_with, $mode='sent' );
		$userpro_msg->do_chat_dir( $chat_with, $chat_from, $mode='inbox' );
		if(userpro_msg_get_option('default_msg')==1)
         	$chat_body=$chat_body."<br><br><br><p style=font-size:10px;color:gray;>".stripslashes( esc_attr(userpro_msg_get_option('default_msg_text')) )."</p>";	
		$userpro_msg->write_chat( $chat_from, $chat_with, $chat_body, $mode='sent' );
		$userpro_msg->write_chat( $chat_with, $chat_from, $chat_body, $mode='inbox' );
		$onlinestatus=$userpro->is_user_online($chat_with);
		
		if($onlinestatus!="1" && userpro_msg_get_option('send_new_message_mail_user')=="1")
		{	
			$userpro_msg->email_user($chat_with, $chat_from, $chat_body);
		}
		
		$userpro_msg->remove_unread_chat($chat_from, $chat_with);
		
		/* Status for browser */
		$output['message'] = '<div class="userpro-msg-notice">'.__('Your message has been sent successfully.','userpro-msg').'</div>';
		
		ob_start();
		require_once userpro_msg_path . 'templates/conversation.php';
		$output['html'] = ob_get_contents();
		ob_end_clean();
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}

/*****************************************************Code for broadcast message*************************************************/	
	add_action('wp_ajax_nopriv_userpro_broadcast_msg', 'userpro_broadcast_msg');
	add_action('wp_ajax_userpro_broadcast_msg', 'userpro_broadcast_msg');
	function userpro_broadcast_msg(){
		global $userpro, $userpro_msg;
		$output = array();
		$user_id = $_POST['user_id'];		
		if ( !userpro_is_logged_in() || $user_id != get_current_user_id() ) die();
		
		ob_start();
		
		require_once userpro_msg_path . 'templates/broadcast.php';

		$output['html'] = ob_get_contents();
		
		ob_end_clean();
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}

	add_action('wp_ajax_nopriv_userpro_broadcast', 'userpro_broadcast');
	add_action('wp_ajax_userpro_broadcast', 'userpro_broadcast');
	function userpro_broadcast(){
		global $userpro, $userpro_msg , $wpdb;
		$output = '';
		$broadcast_body = $_POST['broadcast_body'];
                $follower_id_arr = array();
		$follower_id_arr =get_user_meta( get_current_user_id(), '_userpro_followers_ids', true );
		if( !empty( $follower_id_arr ) ) {		
                $follower_id_arr = array_keys( $follower_id_arr );
		}
		$user_id=get_current_user_id();
		if ( !userpro_is_logged_in() || $user_id != get_current_user_id() ) die();
		$no_of_users;
		$sent_count = 0;
		$blog_id = get_current_blog_id();
	
		$receiver_roles = strtolower(trim(userpro_msg_get_option('roles_that_can_recieve_broadcast')));
		$registered_users = array();
		if($receiver_roles!=''){
			$receiver_roles = explode(',',$receiver_roles);
			$receiver_roles_count = count($receiver_roles);
			$meta_query = array(
    					'key' => $wpdb->get_blog_prefix($blog_id) . 'capabilities',
    					'value' => '"(' . implode('|', array_map('preg_quote', $receiver_roles)) . ')"',
    					'compare' => 'REGEXP'
						);
						$user_query = new WP_User_Query(array('meta_query' => array($meta_query)));
						$users = $user_query->results;
						if(!empty($users)){
						
							foreach ($users as $user){
								if($user->ID != $user_id && ((!empty($follower_id_arr) &&  in_array( $user->ID, $follower_id_arr)) || userpro_msg_get_option('broadcast_followers')=='0')){
									
										$registered_users[] = $user->ID;
									
								}
							}
						}
		}
		
		else{
			
			$users = get_users(array('blog_id'=>$blog_id,'exclude'=>array($user_id)));
			foreach($users as $user){
				
				if( in_array( $user->ID, $follower_id_arr) || userpro_msg_get_option('broadcast_followers')=='0' ){
			
				$registered_users[] = $user->ID;
				}
			}
		}
                $registered_users[] = get_current_user_id();
		$no_of_users = count($registered_users);
		if($no_of_users>0){
		setup_email_cron($registered_users,$user_id, $broadcast_body);
		for($i=0;$i<$no_of_users;$i++){
		if($registered_users[$i]!=$user_id)
		{
			$userpro_msg->do_chat_dir( $registered_users[$i], $user_id, $mode='inbox' );
                        $userpro_msg->write_chat( $registered_users[$i], $user_id, $broadcast_body, $mode='inbox' );
                        $userpro_msg->do_chat_dir( $user_id, $registered_users[$i], $mode='sent' );
			$userpro_msg->write_chat( $user_id, $registered_users[$i], $broadcast_body, $mode='sent' );
			$sent_count++;
			$message = 'Message sent to '.$sent_count.' out of '.($no_of_users).' users.';
		}
		else
		{
                    $message = "Broadcasting message...";
		}
		$_SESSION['message'] = $message;
		session_write_close();
	  }
	  	echo "Your message broadcasted successfully.";
	}
	else{
		echo 'No users found to broadcast the message.';
	}
	 die();
	}

	add_action('wp_ajax_nopriv_userpro_check_broadcast_progress', 'userpro_check_broadcast_progress');
	add_action('wp_ajax_userpro_check_broadcast_progress', 'userpro_check_broadcast_progress');
	
	function userpro_check_broadcast_progress()
	{
		session_start();
		$message = '';
		if(isset($_SESSION['message']))
		{
			$message = $_SESSION['message'];
		}
		echo $message;
		die();
	}
	
	function send_broadcast_emails($registered_users,$user_id, $broadcast_body,$unique_key=null){
		global $userpro_msg;
		$users_count = count($registered_users);
		
		for($i=0;$i<$users_count;$i++){
			if($registered_users[$i]!=$user_id)
			{
				$userpro_msg->email_user($registered_users[$i], $user_id, $broadcast_body);
			}	
		}
				$userpro_msg->email_broadcaster($user_id, $broadcast_body);
	}
	
	function setup_email_cron($registered_users,$user_id, $broadcast_body){
		
		$timestamp = wp_next_scheduled( 'send_broadcast_emails');
		if(empty($timestamp))
			$timestamp = time();
		
		$unique_key = sha1(site_url().$timestamp);
		wp_schedule_single_event($timestamp	, 'send_broadcast_emails' , array($registered_users,$user_id, $broadcast_body,$unique_key));
	}
	add_action('send_broadcast_emails','send_broadcast_emails',10,4);
		
	add_action('wp_head','get_translated_text_for_alert');
	function get_translated_text_for_alert(){
		$up_user = wp_get_current_user();
                $allowed_conn_roles=  strtolower(trim(userpro_msg_get_option('roles_that_can_send_message_for_connections')));
                if(!empty($allowed_conn_roles)){
                    $allowed_conn_roles=explode(',',$allowed_conn_roles);
                }
		if(userpro_msg_get_option('allow_msg_connections')=='1' && ((!empty($allowed_conn_roles) && isset($up_user->roles[0]) && in_array($up_user->roles[0],$allowed_conn_roles)) || empty($allowed_conn_roles)  ) ){
		?>
			<script type="text/javascript">
			var translated_text_for_connection_msg_alert = '<?php _e("This message will be sent immediately to all your connections. Are you sure you want to send this ?","userpro-msg") ?>';
			</script>
			<?php	
		}
				
		if(userpro_msg_get_option('broadcast_followers')=='0') {?>
		<script type="text/javascript">
		var translated_text_for_alert = '<?php _e("This message will be sent immediately to ALL registered users. Are you sure you want to send this ?","userpro-msg") ?>';
		</script>
		<?php }
		else
		{?>
		<script type="text/javascript">
		var translated_text_for_alert = '<?php _e("This message will be sent immediately to all your followers. Are you sure you want to send this ?","userpro-msg") ?>';
		</script>
		<?php }
	}
	add_action('wp_ajax_nopriv_userpro_chk_msg_notification', 'userpro_chk_msg_notification');
	add_action('wp_ajax_userpro_chk_msg_notification', 'userpro_chk_msg_notification');
	
	function userpro_chk_msg_notification() {
		global $userpro_msg;	
		if (userpro_is_logged_in()){
			$user_id = get_current_user_id();
			if ($userpro_msg->has_new_chats($user_id)) {
				require_once userpro_msg_path . 'templates/notification.php';
			}
		}
		die();
	}
	
/*****************************************************Code end*************************************************/


