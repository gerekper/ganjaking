<?php
// short code for my message
add_shortcode('userpro_mymessage','userpro_msg_mymsg');
function userpro_msg_mymsg()
{
	$user_id=get_current_user_id(); 
	if($user_id==0)
		return "Please login to see messages";
	else{
		$mymsgstr = '<a class="userpro-show-chat dt-btn dt-btn-s" href="#" data-user_id="'.$user_id.'">My Messages</a>' ;
		return $mymsgstr;
	    }
} 

add_shortcode('userpro_message_list','userpro_message_list');
function userpro_message_list()
{
	global $userpro,$userpro_msg;
	if(is_user_logged_in())
	{
	include_once userpro_msg_path . 'templates/message_list.php';
	}
	else
	{
		echo 'Please <a href="#" class="popup-login">login</a> to view this area.';
	}
}
?>
