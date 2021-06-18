<?php

function userpro_messaging_check_blockuser($user_id)
{

	$current_user = get_current_user_id();	
	$blockusers=get_user_meta( $current_user,"_userpro_messaging_block_users",true);
	$flag=0;
	if(is_array($blockusers)){		
		foreach ($blockusers as $block);
		{	
			if($block==$user_id)
				$flag=1;
			else
				$flag=0;
		}
	}
	return $flag;
}

function userpro_messaging_check($user_id)
{
		
			$rolethat_can_send=userpro_msg_get_option('roles_that_can_send_message');
		$rolethat_can_recive=userpro_msg_get_option('roles_that_can_recive_message');
			$allowed=0;
		$user = new WP_User(  $user_id );
		if($user_id != 0 && !empty($user)){
			$user_roles = $user->roles;
			if(isset($user_roles)){
				$chat_with_userrole= $user_roles[0];
			}
		}

		if(empty($rolethat_can_send) || empty($rolethat_can_recive) )
		{
			$allowed = 1;
		}
		$rolethat_can_sends=explode(',',$rolethat_can_send);
		$rolethat_can_recive=explode(',',$rolethat_can_recive);
		foreach($rolethat_can_sends as $role_can_send)
		{
				
			if(current_user_can($role_can_send) && in_array($chat_with_userrole,$rolethat_can_recive) )
			$allowed=1;

			
		}
		

		return $allowed;

}

	/* Send a default welcome message to each new user */
	add_action('userpro_after_new_registration', 'userpro_msg_welcome', 9999);
	function userpro_msg_welcome($user_id) {
		global $userpro_msg;
		
		if ( userpro_msg_get_option('msg_auto_welcome') && userpro_msg_get_option('msg_auto_welcome_text') && userpro_msg_get_option('msg_auto_welcome_id') ) {
		
		$chat_from = userpro_msg_get_option('msg_auto_welcome_id');
		$chat_with = $user_id;
		
		$chat_body = stripslashes( userpro_msg_get_option('msg_auto_welcome_text') );

		$userpro_msg->do_chat_dir( $chat_from, $chat_with, $mode='sent' );
		$userpro_msg->do_chat_dir( $chat_with, $chat_from, $mode='inbox' );
		
		$userpro_msg->write_chat( $chat_from, $chat_with, $chat_body, $mode='sent' );
		$userpro_msg->write_chat( $chat_with, $chat_from, $chat_body, $mode='inbox' );
		
		$userpro_msg->email_user($chat_with, $chat_from, $chat_body);
		
		}
		
	}

	/* Enqueue Scripts */

	
	add_action('wp_enqueue_scripts', 'userpro_msg_enqueue_scripts', 99);
	function userpro_msg_enqueue_scripts(){
	
		wp_register_style('userpro_msg', userpro_msg_url . 'css/userpro-msg.css');
		wp_enqueue_style('userpro_msg');
		
		wp_register_style('userpro_mcsroll', userpro_msg_url . 'css/jquery.mCustomScrollbar.css');
		wp_enqueue_style('userpro_mcsroll');
		
		wp_register_script('userpro_msg', userpro_msg_url . 'scripts/userpro-msg.js');
		
		wp_enqueue_script('userpro_msg');
		$enterforsend=userpro_msg_get_option('enterforsend');
		$translation_array=array('value' => $enterforsend); 
		wp_localize_script( 'userpro_msg','obj', $translation_array );
		
		wp_register_script('userpro_textarea_auto', userpro_msg_url . 'scripts/jquery.textareaAutoResize.js');
		wp_enqueue_script('userpro_textarea_auto');
	
		wp_register_script('userpro_mousewheel', userpro_msg_url . 'scripts/jquery.mousewheel.min.js');
		wp_enqueue_script('userpro_mousewheel');
		
		wp_register_script('userpro_mcsroll', userpro_msg_url . 'scripts/jquery.mCustomScrollbar.min.js');
		wp_enqueue_script('userpro_mcsroll');
		
	}
	
	/* Add messages button / send chat button to profile */
	if( userpro_msg_get_option('show_send_message') == 1 ){
		add_action('userpro_social_buttons', 'userpro_msg_profile_buttons');
	}
	function userpro_msg_profile_buttons( $user_id ){
		
		global $userpro_msg;
	
	$following=0;
	$allowed=0;
	$allowed_roles=get_option('roles_that_can_send');
	$allowed_roles = preg_replace('/\s+/', '', $allowed_roles);
	
	$array = get_user_meta($user_id,'_userpro_followers_ids');
	$follower_id_arr = get_user_meta( $user_id, '_userpro_following_ids', true );
	$follower = 0;
	if( isset( $follower_id_arr ) && is_array( $follower_id_arr ) && userpro_msg_get_option('user_followers')){
		$follower_id_arr = array_keys( $follower_id_arr );
		if( in_array( get_current_user_id(), $follower_id_arr )){
			$follower = 1;
		}
	}
	if(isset($array['0']))
	{	
		foreach($array['0'] as  $key => $val)
		{	
		
			if(get_current_user_id()==$key )
			{
				$following=1;
		
			}
		}
	}			
		if(userpro_msg_get_option('following_user')=='0')
		$following=0;
		$res = null;
		
		if(empty($allowed_roles))
		{
			$allowed = 1;
		}
		$allowed_roles=explode(',',$allowed_roles);
		foreach($allowed_roles as $allowed_role)
		{
			if(current_user_can($allowed_role))
			{
				$allowed = 1;
			}
		}
		$up_user = wp_get_current_user();
                $allowed_conn_roles=  strtolower(trim(userpro_msg_get_option('roles_that_can_send_message_for_connections')));
                if(!empty($allowed_conn_roles)){
                    $allowed_conn_roles=explode(',',$allowed_conn_roles);
                }
		$msgto_connections = userpro_msg_get_option('allow_msg_connections');
		$up_global_userconnect = userpro_get_option('enable_connect');
		if( $allowed == 1 && $up_global_userconnect == 'y' && $msgto_connections == '1' && ( (!empty($allowed_conn_roles)  && isset($up_user->roles[0]) && in_array($up_user->roles[0],$allowed_conn_roles) ) || empty($allowed_conn_roles) ) ){
			$res .= '<a href="#" class="userpro-button chat userpro-msg-connections" data-user_id="'.$user_id.'"><i class="userpro-icon-connection"></i>'.__('Send Message to Connections','userpro-msg');
				
			$res .= '</a> ';
		
		}
		
		
		$current_user = get_current_user_id();	
		$blockusers=get_user_meta( $current_user,"_userpro_messaging_block_users",true);
		if ( $userpro_msg->can_chat_with( $user_id ) ) {
		$allowed=0;
		$block ='';
		$flag=0;
		
		if(is_array($blockusers)){		
		foreach ($blockusers as $block);
		{	if($block==$user_id)
			$flag=1;
			else
			$flag=0;
		
		}
		}
		
		$result=userpro_messaging_check($user_id);
		
			if(($allowed == 1 && $result == 1 && $flag == 0) || (current_user_can('administrator'))   )
			{
				$show = 0;
				if( $following || $follower ){
					$show = 1;
				}
				if( !userpro_msg_get_option('user_followers') && !userpro_msg_get_option('following_user'))
					$show = 1;
			if( $show )
			{
				$res = '<a href="#" class="userpro-button chat userpro-init-chat" data-chat_with="'.$user_id.'" data-chat_from="'.get_current_user_id().'"><i class="userpro-icon-comment"></i>'.__('Send Message','userpro-msg');
				$res .= '</a>';
			}
			}
			
		} else if (userpro_is_logged_in() && $user_id == get_current_user_id() ) {
			//$res="";
			$broadcast_status=userpro_msg_get_option('broadcast_enabled');
			if($broadcast_status==1){
				$allowed=0;
				$allowed_roles=  strtolower(trim(userpro_msg_get_option('roles_that_can_broadcast')));
				$allowed_roles=explode(',',$allowed_roles);
				foreach($allowed_roles as $allowed_role)
				{
					if(current_user_can($allowed_role))
					{
						$allowed=1;
					}
				}
				if($allowed==1 )
				{
					$res .= '<a href="#" class="userpro-button chat userpro-broadcast-msg" data-user_id="'.$user_id.'"><i class="userpro-icon-globe"></i>'.__('Broadcast Message','userpro-msg');
 
					$res .= '</a> ';
				}
			}
			
			$res .= '<a href="#" class="userpro-button secondary userpro-show-chat userpro-tip" data-user_id="'.$user_id.'" title="'.$userpro_msg->new_chats_notifier($user_id).'"><i class="userpro-icon-comments"></i>'.__('My Messages','userpro-msg');
			
			if ($userpro_msg->new_chats_notifier_count($user_id) > 0 ) {
				$res .= '<span>'.$userpro_msg->new_chats_notifier_count($user_id).'</span>';
			}
			
			$res .= '</a>';
			
		}
		
		echo $res;
		
	}
	
	/* Add chat/message badge */
	add_filter('userpro_after_all_badges','userpro_show_msg_icon', 99, 1);
	function userpro_show_msg_icon($user_id){
        
		global $userpro_msg;
		$array = array();	
		$following=0;
	$array = get_user_meta($user_id,'_userpro_followers_ids');
	$follower_id_arr = get_user_meta( $user_id, '_userpro_following_ids', true );
	$follower = 0;
	if( isset( $follower_id_arr ) && is_array( $follower_id_arr ) && userpro_msg_get_option('user_followers')){
		$follower_id_arr = array_keys( $follower_id_arr );
		if( in_array( get_current_user_id(), $follower_id_arr )){
			$follower = 1;
		}
	}
	if(isset($array['0']))
	{	
		foreach($array['0'] as  $key => $val)
		{	
		
			if(get_current_user_id()==$key )
			{
				$following=1;
		
			}
		}
	}	
		if(userpro_msg_get_option('following_user')=='0')
		$following=0;
		$res = '';
		$allowed=0;
		$current_user = get_current_user_id();	
		$allowed_roles=get_option('roles_that_can_send');
		$blockuserslist=get_user_meta( $current_user,"_userpro_messaging_block_users",true);
		$blockuserlist=get_user_meta( $current_user,"_userpro_messaging_block_users_list",true);
		$allowed_roles = preg_replace('/\s+/', '', $allowed_roles);
		$flag=0;
		$block='';
		$blockuserflag=0;
		if(is_array($blockuserslist)){
		foreach ($blockuserslist as $block);
		{	if($block==$user_id)
			$blockuserflag=1;
			else
			$blockuserflag=0;
			
		}
		}
		if(is_array($blockuserlist)){
		foreach ($blockuserlist as $block);
		{	if($block==$user_id)
			$flag=1;
			else
			$flag=0;

			
		}
	      }
		if(empty($allowed_roles))
		{
			$allowed = 1;
		}
			$allowed_roles=explode(',',$allowed_roles);
			foreach($allowed_roles as $allowed_role)
			{
				if(current_user_can($allowed_role))
				{
					$allowed = 1;
				}
			}
		$result=userpro_messaging_check($user_id);
	
			
			
			if((($allowed == 1 && $result==1) || (current_user_can('administrator'))) && $flag == 0 && $blockuserflag==0  )
			{
				$show = 0;
				if( $following || $follower ){
					$show = 1;
				}
				if( !userpro_msg_get_option('user_followers') && !userpro_msg_get_option('following_user'))
					$show = 1;
				if ( $userpro_msg->can_chat_with( $user_id ) ) {
					if( $show )
					{
						$res = '<img data-chat_with="'.$user_id.'" data-chat_from="'.get_current_user_id().'" class="userpro-profile-badge userpro-profile-badge-msg userpro-init-chat" src="'.userpro_msg_url . 'img/icon-chat-small.png" alt="" title="'.__('Send a message','userpro-msg').'" />';
		
					}	
				} else if (userpro_is_logged_in() && $user_id == get_current_user_id() ) {
			
					$res = '<img data-user_id="'.get_current_user_id().'" class="userpro-profile-badge userpro-profile-badge-msg userpro-show-chat" src="'.userpro_msg_url . 'img/icon-messages-small.png" alt="" title="'.$userpro_msg->new_chats_notifier($user_id).'" />';
		
				}
			}
			else 
			{
				
				
						
						if($blockuserflag==0 && $result!=0 ){
							if( $following || $follower || ( !userpro_get_option('user_followers') && !userpro_get_option('following_user') ) ){
								$res = '<span class="unblock_user"><img data-user_id="'.$user_id.'" class="userpro-profile-badge userpro-profile-badge-msg userpro-unblock-user" src="'.userpro_msg_url . 'img/unblock.png" alt="" /></span>';
							}	
							}
				
			}	
		
		return $res;
	}
	
	

	/* Notification of new messages */
	add_filter('wp_footer', 'userpro_show_new_notification_div');
	function userpro_show_new_notification_div(){
		echo '<div id="msg_notification"></div>';
		
		?>
			<script>
			jQuery(function(){
				jQuery.ajax({
					url:userpro_ajax_url,
					data: "action=userpro_chk_msg_notification",
					type: 'POST',
					success:function(data){
						jQuery('#msg_notification').html(data);
					},
				});
				<?php 
				if(userpro_msg_get_option('autorefresh')){
				?>
				setInterval(function(){ 
					jQuery.ajax({
						url:userpro_ajax_url,
						data: "action=userpro_chk_msg_notification",
						type: 'POST',
						success:function(data){
							jQuery('#msg_notification').html(data);
						},
					});
				}, 30000);
				<?php 
				}
				?>
			});
			</script>
		<?php 
		}


add_filter('updb_default_options_array','upm_sendmessage_in_dashboard','10','1');
function upm_sendmessage_in_dashboard($array){

	$template_path= userpro_msg_path.'templates/';
	$olddata=$array['updb_available_widgets'];
	$newdata= array ('sendmessage'=>array('title'=>'Send a Message', 'template_path'=>$template_path ));
	$array['updb_available_widgets']=   array_merge($olddata,$newdata);
	
	$oldunsetwidgets=$array['updb_unused_widgets'];
	$newunsetwidgets= array( 'sendmessage');
	$array['updb_unused_widgets']= array_merge($oldunsetwidgets,$newunsetwidgets);
	
	return $array;

}