<?php

class userpro_msg_api {

	/* Constructor */
	function __construct() {
		
		
	}
	
		
	/* online status for user */
	function online_status($user_id) {
		global $userpro;
		$res = null;
		if (userpro_get_option('modstate_online')) {
			if ($userpro->is_user_online($user_id)) {
				$res = userpro_get_badge('online');
			} else {
				$res = userpro_get_badge('offline');
			}
		}
		return $res;
	}
	
	/* Email user about new chat */
	function email_user($to, $from, $msg) {
		global $userpro;
		if (userpro_msg_get_option('email_notifications') ==  1 ) {
		
		$user = get_userdata($to);
		$display_name = userpro_profile_data('display_name', $from);

		// message
		$msg = stripslashes($msg);
		$val = $this->replace_placeholders($msg,$from,$to);
		$search = $val['search'];
		$replace = $val['replace'];
		$subject = userpro_msg_get_option("mail_new_msg_s");
		$subject = str_replace( $search, $replace, $subject );
		$body = html_entity_decode(nl2br(userpro_msg_get_option("mail_new_msg")));
		$body = str_replace( $search, $replace, $body );
		
		$headers = 'From: '.userpro_get_option('mail_from_name').' <'.userpro_get_option('mail_from').'>' . "\r\n";
		$headers .= "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		
		wp_mail( $user->user_email , $subject, $body, $headers );
		
		}
	}
	
	/* Send Email to the user who is broadcasting the message */
	function email_broadcaster($to,$msg){
		global $userpro;
		if (userpro_msg_get_option('email_notifications') ==  1 ) {
		
		$user = get_userdata($to);
		$display_name = userpro_profile_data('display_name', userpro_get_option('mail_from'));

		// message
		$msg = stripslashes($msg);
		$val = $this->replace_placeholders($msg,$to);
		$search = $val['search'];
		$replace = $val['replace'];
		$subject = userpro_msg_get_option("mail_broadcast_msg_s");
		$subject = str_replace( $search, $replace, $subject );
		$body = html_entity_decode(nl2br(userpro_msg_get_option("mail_broadcast_msg")));
		$body = str_replace( $search, $replace, $body );
		$headers = 'From: '.userpro_get_option('mail_from_name').' <'.userpro_get_option('mail_from').'>' . "\r\n";
		$headers .= "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		wp_mail( $user->user_email , $subject, $body, $headers );

		}
	}
	
	function replace_placeholders($message,$from_id=null,$to_id=null){
		global $userpro;
		$builtin = array(
				'{USERPRO_ADMIN_EMAIL}' => userpro_get_option('mail_from'),
				'{USERPRO_BLOGNAME}' => userpro_get_option('mail_from_name'),
				'{USERPRO_BLOG_URL}' => home_url(),
				'{USERPRO_BLOG_ADMIN}' => admin_url(),
				'{USERPRO_MESSAGE}'	=> $message
		);
		
		if(isset($from_id)){
			$from_user = get_userdata($from_id);
			$builtin['{USERPRO_FROM_USERNAME}'] = $from_user->user_login;
			$builtin['{USERPRO_FROM_FIRST_NAME}'] = userpro_profile_data('first_name', $from_user->ID );
			$builtin['{USERPRO_FROM_LAST_NAME}'] = userpro_profile_data('last_name', $from_user->ID );
			$builtin['{USERPRO_FROM_NAME}'] = userpro_profile_data('display_name', $from_user->ID );
			$builtin['{USERPRO_FROM_EMAIL}'] = $from_user->user_email;
			$builtin['{USERPRO_FROM_PROFILE_LINK}'] = $userpro->permalink( $from_user->ID );
		}
		
		if(isset($to_id)){
			$to_user = get_userdata($to_id);
			$builtin['{USERPRO_TO_USERNAME}'] = $to_user->user_login;
			$builtin['{USERPRO_TO_FIRST_NAME}'] = userpro_profile_data('first_name', $to_user->ID );
			$builtin['{USERPRO_TO_LAST_NAME}'] = userpro_profile_data('last_name', $to_user->ID );
			$builtin['{USERPRO_TO_NAME}'] = userpro_profile_data('display_name', $to_user->ID );
			$builtin['{USERPRO_TO_EMAIL}'] = $to_user->user_email;
			$builtin['{USERPRO_TO_PROFILE_LINK}'] = $userpro->permalink( $to_user->ID );
		}
		$search = array_keys($builtin);
		$replace = array_values($builtin);
		return array( 'search'=>$search, 'replace'=>$replace );
		
	}
	
	/* Remove unread chat */
	function remove_unread_chat($user1, $user2) {
		if ( file_exists( $this->get_conv_unread($user1, $user2) ) ) {
			unlink ( $this->get_conv_unread($user1, $user2) );
		}
	}
	
	/* Remove archive chat */
	function remove_read_chat($user1, $user2) {
		if ( file_exists( $this->get_conv_read($user1, $user2) ) ) {
			unlink ( $this->get_conv_read($user1, $user2) );
		}
	}
	
	/* load the chat/quick reply form */
	function load_chat_form($chat_from, $chat_with) {

		$allowed_roles=get_option('roles_that_can_send');
		$allowed_roles = preg_replace('/\s+/', '', $allowed_roles);
		$result=userpro_messaging_check($chat_with);
	



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
			
	$blockuser=userpro_messaging_check_blockuser($chat_with);
	
	if(($allowed == 1 && $result == 1 && $blockuser==0)|| (current_user_can('administrator'))  )
	{


		$output = '<form action="" method="post" class="userpro-send-chat">
		<div class="userpro-msg-result"></div>
		<div class="userpro-msg-field">
		<input type="hidden" name="chat_with" id="chat_with" value="'.$chat_with.'" />
		<input type="hidden" name="chat_from" id="chat_from" value="'.$chat_from.'" />
		<textarea placeholder="'.__('Type your message here...','userpro-msg').'" name="chat_body" id="chat_body"></textarea>
		</div>
		<div class="userpro-msg-submit">
		<div class="userpro-msg-left"><input type="submit" value="'.__('Send Message','userpro-msg').'" disabled="disabled" /><img src="'.userpro_msg_url. 'img/loading-dots.gif" alt="" /></div>
		<div class="userpro-msg-right"><input type="button" value="'.__('Cancel','userpro-msg').'" /></div>
		<div class="userpro-clear"></div>
		</div>
		</form>';
		}	 else {
			$output = '<form action="" method="post" class="userpro-send-chat">
			<div class="userpro-msg-result"></div>
			<div class="userpro-msg-field">
			<input type="hidden" name="chat_with" id="chat_with" value="'.$chat_with.'" />
			<input type="hidden" name="chat_from" id="chat_from" value="'.$chat_from.'" />
			</div>
			</form>';
		}
return $output;
}
/***************************************Code for broadcast************************************************************/	
	function load_broadcast_form($user_id) {
		$output = '<form action="" method="post" class="userpro-broadcast">
			<div class="userpro-msg-result"></div>
			<div class="userpro-msg-field">
				<input type="hidden" name="user_id" id="user_id" value="'.$user_id.'" />
				<textarea placeholder="'.__('Type your message here...','userpro-msg').'" name="broadcast_body" id="broadcast_body"></textarea>
			</div>
			<div class="userpro-msg-submit">
				<div class="userpro-msg-left"><input type="submit" value="'.__('Broadcast','userpro-msg').'" disabled="disabled" /><img src="'.userpro_msg_url. 'img/loading-dots.gif" alt="" /></div>
				<div class="userpro-msg-right"><input type="button" value="'.__('Cancel','userpro-msg').'" /></div>
				<div class="userpro-clear"></div>
			</div>
			</form>';
		return $output;
	}
/***************************************Code end****************************************************************/

	function load_msg_connections_form($user_id){
		
		$output = '<form action="" method="post" class="userpro-connection">
			<div class="userpro-msg-result"></div>
			<div class="userpro-msg-field">
				<input type="hidden" name="user_id" id="user_id" value="'.$user_id.'" />
				<textarea placeholder="'.__('Type your message here...','userpro-msg').'" name="connection_msg_body" id="connection_msg_body"></textarea>
			</div>
			<div class="userpro-msg-submit">
				<div class="userpro-msg-left"><input type="submit" value="'.__('Send','userpro-msg').'" disabled="disabled" /><img src="'.userpro_msg_url. 'img/loading-dots.gif" alt="" /></div>
				<div class="userpro-msg-right"><input type="button" value="'.__('Cancel','userpro-msg').'" /></div>
				<div class="userpro-clear"></div>
			</div>
			</form>';
		return $output;
		
	}
	
	
	/* Emotize */
	function emotize($content) {
            
		$content =  str_ireplace('<3>','< 3 >',$content);
                $content =  str_ireplace('<3','<3>',$content);
		if( userpro_msg_get_option('allow_html_content') == 1 ){
			$allowed_html = "<a><br><b><center><div><em><h1><h2><h3><h4><h5><h6><i><p><u><Strikeout><strong><sub><sup><3>";
			$content = strip_tags($content,$allowed_html);
		}
		else{
                    
			$content = strip_tags($content,"<br><p><3>");
		}
		$img = '<img src="'.userpro_msg_url . 'img/emoticons/{symbol}.png" class="userpro-emo" alt="" />';
		$content = str_replace(':love', str_replace('{symbol}','heart',$img), $content);
		$content = str_replace('(y)', str_replace('{symbol}','like',$img), $content);
		$content = str_replace(':)', str_replace('{symbol}','smile',$img), $content);
		$content = str_replace(':(', str_replace('{symbol}','frown',$img), $content);
		$content = str_replace(":'(", str_replace('{symbol}','cry',$img), $content);
		$content = str_replace('o:)', str_replace('{symbol}','angel',$img), $content);
		$content = str_replace(':o', str_replace('{symbol}','gasp',$img), $content);
		$content = str_replace(':D', str_replace('{symbol}','grin',$img), $content);
		$content = str_replace(':nerd', str_replace('{symbol}','glasses',$img), $content);
		$content = str_replace(':cool', str_replace('{symbol}','sunglasses',$img), $content);
		$content = str_replace(':p', str_replace('{symbol}','tongue',$img), $content);
		$content = str_replace(':confused', str_replace('{symbol}','unsure',$img), $content);
		$content = str_replace(';)', str_replace('{symbol}','wink',$img), $content);
		$content = str_replace(':kiss', str_replace('{symbol}','kiss',$img), $content);
                $content = str_replace('<3>', str_replace('{symbol}','heart',$img), $content);
                $content = str_replace('8)', str_replace('{symbol}','sunglasses',$img), $content);
                $content = str_replace(':evil:', str_replace('{symbol}','evil',$img), $content);
                $content = str_replace(':lol:', str_replace('{symbol}','lol',$img), $content);
                $content = str_replace(':x', str_replace('{symbol}','mad',$img), $content);
	
		$content = autolink($content);
		
		return $content;
	}
	
	/* Extract a msg content used in conversation */
	function get_msg_content($item) {
		global $userpro;
		if ( strlen($item) > 10 ) {
		
			$mode = preg_match('#\[mode\](.*?)\[\/mode\]#', $item, $matches);
			$mode = $matches[1];
			$result['mode'] = $mode;
			
			$content = preg_match('/\[content\](.*?)\[\/content\]/ism', $item, $matches);
			$content = $matches[1];
			$content = stripslashes($content);
			$content = $this->emotize($content);
			$result['content'] = $content;
			
			$timestamp = preg_match('#\[timestamp\](.*?)\[\/timestamp\]#', $item, $matches);
			$timestamp = $matches[1];
			$result['timestamp'] = $userpro->time_elapsed( $timestamp );
		
			return $result;
			
		}
	}
	
	/* Get latest message between 2 users */
	function extract_msg($user_id, $id, $folder, $element, $pos=null) {
		global $userpro;
		
		/* unread vs archive */
		if ($folder == 'unread') {
		$conversation = $this->get_conv_unread($user_id, $id);
		} else {
		$conversation = $this->get_conv_read($user_id, $id);
		}
		$content = file_get_contents($conversation);
		$content = explode('[/]', $content);
		
		/* last message */
		if ($pos == 1) {
		$content = $content[0];
		}
		
		if ($element == 'mode') {
		$content = preg_match('#\[mode\](.*?)\[\/mode\]#', $content, $matches);
		$content = $matches[1];
		return $content;
		}
		
		if ($element == 'unread_msgs_count') {
			return count($content) - 1;
		}
		
		if ($element == 'content') {
		$content = preg_match('/\[content\](.*?)\[\/content\]/ism', $content, $matches);
		$content = $matches[1];
		$content = stripslashes($content);
		$content = explode (' ', $content);
		if (count($content) <= 20) {
			$content = implode(' ', $content);
			$content = $this->emotize($content);
			return $content;
		} else {
			$content = array_slice ($content, 0, 20);
			$content = implode(' ', $content);
			$content = $this->emotize($content);
			return $content . '...';
		}
		}
		
		if ($element == 'status') {
		$content = preg_match('#\[status\](.*?)\[\/status\]#', $content, $matches);
		$content = $matches[1];
		return $content;
		}
		
		if ($element == 'timestamp') {
		$content = preg_match('#\[timestamp\](.*?)\[\/timestamp\]#', $content, $matches);
		$content = $matches[1];
		return $userpro->time_elapsed( $content );
		}
		
	}
	
	/* Get unread conversations of user */
	function get_unread_user_ids($user_id) {
		$unread = $this->get_conv_unread_folder($user_id);
		if ( !$this->is_dir_empty($unread) ) {
			foreach (glob( $unread . '*.txt') as $user) {
				$modified = filemtime( $user );
				$id = str_replace('.txt','', basename($user));
				$ids[] = array(
				
					'id' => $id,
					'modified' => $modified
					
				);
			}
		}
		
		if (isset($ids)) {
			$this->array_sort_by_column($ids, 'modified', SORT_DESC);
			foreach($ids as $k => $v) {
				$ordered[] = $v['id'];
			}
			return $ordered;
		} else {
			return '';
		}
	}
	
	/* Get read conversations of user */
	function get_read_user_ids($user_id) {
		$read = $this->get_conv_read_folder($user_id);
		if ( !$this->is_dir_empty($read) ) {
			foreach (glob( $read . '*.txt') as $user) {
				$modified = filemtime( $user );
				$id = str_replace('.txt','', basename($user));
				$ids[] = array(
				
					'id' => $id,
					'modified' => $modified
					
				);
			}
		}
		
		if (isset($ids)) {
			$this->array_sort_by_column($ids, 'modified', SORT_DESC);
			foreach($ids as $k => $v) {
				$ordered[] = $v['id'];
			}
			return $ordered;
		} else {
			return '';
		}
	}
	
	/* Sort array */
	function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
		$sort_col = array();
		foreach ($arr as $key=> $row) {
			$sort_col[$key] = $row[$col];
		}

		array_multisort($sort_col, $dir, $arr);
	}
	
	/* Show user conversations */
	function conversations($user_id) {
		global $userpro;
		$output = null;
		
		$unread = $this->get_unread_user_ids($user_id);
		$archive = $this->get_read_user_ids($user_id);
		
		if (isset($archive) && !empty($archive) && isset($unread) && !empty($unread) ){
		$archive = array_diff($archive, $unread);
		}
		
		if (isset($unread) && !empty($unread)) {
			foreach ( $unread as $id) {
				$output .= '<div class="userpro-msg-col" data-chat_from="'.$user_id.'" data-chat_with="'.$id.'">
				
					<span class="userpro-msg-view"><i class="userpro-icon-retweet"></i>'.__('read conversation','userpro-msg').'</span>
									
					<div class="userpro-msg-user-thumb alt">'.get_avatar($id, 40).'</div>
					
					<div class="userpro-msg-user-info">
						
						<div class="userpro-msg-user-name alt">
							<span>'.userpro_profile_data('display_name', $id).'</span>
							<span class="bubble" data-chat_with="'.$id.'"><i class="userpro-icon-comment"></i></span>
							<span class="bubble-text">'.__('quick reply','userpro-msg').'</span>';
					if( userpro_msg_get_option('block_user')==1){	
						$output.='	<span class="block" data-block_user="'.$user_id.'"  data-user="'.$id.'"><img src="'.userpro_msg_url. 'img/blocked.png" alt="" /></span>
							<span class="block-text">'.__('Block User','userpro-msg').'</span>';
		}
				$output.='
							
						</div>
						
						
						<div class="userpro-msg-user-tab alt">';
						
				if ( $this->extract_msg($user_id, $id, 'unread', 'status', 1) == 'unread') {
					$output .= '<span class="userpro-msg-unread">'.sprintf(__('%s unread','userpro-msg'), $this->extract_msg($user_id, $id, 'unread', 'unread_msgs_count') ).'</span>';
				}
				
				$output .= $this->extract_msg($user_id, $id, 'unread', 'content', 1);
				
				$output .= '<span class="userpro-msg-toolbar">
								<span class="userpro-msg-timestamp">'.$this->extract_msg($user_id, $id, 'unread', 'timestamp', 1).'</span>
								<span class="userpro-msg-delete"><a href="#" data-chat_from="'.$user_id.'" data-chat_with="'.$id.'">'.__('Delete Conversation','userpro-msg').'</a></span>
							</span>';
				
				$output .= '</div>
						
					</div><div class="userpro-clear"></div>
					</div>';
			}
		}

		if (isset($archive) && !empty($archive)) {
		
			foreach ( $archive as $id) {
				$output .= '<div class="userpro-msg-col" data-chat_from="'.$user_id.'" data-chat_with="'.$id.'">
				
					<span class="userpro-msg-view"><i class="userpro-icon-retweet"></i>'.__('read conversation','userpro-msg').'</span>
									
					<div class="userpro-msg-user-thumb alt">'.get_avatar($id, 40).'</div>
					
					<div class="userpro-msg-user-info">
						
						<div class="userpro-msg-user-name alt">
							<span>'.userpro_profile_data('display_name', $id).'</span>
							<span class="bubble" data-chat_with="'.$id.'"><i class="userpro-icon-comment"></i></span>
							<span class="bubble-text">'.__('quick reply','userpro-msg').'</span>';
			if( userpro_msg_get_option('block_user')==1){			
			$output.='<span class="block" data-block_user="'.$user_id.'"  data-user="'.$id.'"><img src="'.userpro_msg_url. 'img/blocked.png" alt="" /></span>
							<span class="block-text">'.__('Block User','userpro-msg').'</span>';
				}
				$output.='		</div>
						
						<div class="userpro-msg-user-tab alt">';
						
				if ( $this->extract_msg($user_id, $id, 'archive', 'mode', 1) == 'sent') {
					$output .= '<span class="userpro-msg-you"><i class="userpro-icon-reply"></i></span>';
				}
				
				$output .= $this->extract_msg($user_id, $id, 'archive', 'content', 1);
				
				$output .= '<span class="userpro-msg-toolbar">
								<span class="userpro-msg-timestamp">'.$this->extract_msg($user_id, $id, 'archive', 'timestamp', 1).'</span>
								<span class="userpro-msg-delete"><a href="#" data-chat_from="'.$user_id.'" data-chat_with="'.$id.'">'.__('Delete Conversation','userpro-msg').'</a></span>
							</span>';
							
				$output .= '</div>
						
					</div><div class="userpro-clear"></div>
					</div>';
			}
		}
		
		return $output;
	}
	
	/* Check if user can chat with another user */
	function can_chat_with( $user_id ) {
		global $userpro_social;
		$global_privacy = userpro_msg_get_option('msg_privacy');
		if ( $user_id != get_current_user_id() && userpro_is_logged_in() ) {
		
			if ($global_privacy == 'none') {
				return false;
			}
			
			if ($global_privacy == 'public') {
				return true;
			} else if ($global_privacy == 'mutual' && $userpro_social->mutual_follow( get_current_user_id(), $user_id ) ) {
				return true;
			}
		
		} else {
			return false;
		}
	}
	
	/* Get conversation unread folder */
	function get_conv_unread_folder( $user_id ) {
		global $userpro;
		return $userpro->upload_base_dir . $user_id . '/conversations/unread/';
	}
	
	/* Get conversation read folder */
	function get_conv_read_folder( $user_id ) {
		global $userpro;
		return $userpro->upload_base_dir . $user_id . '/conversations/archive/';
	}
	
	/* Get conversation */
	function get_conv_unread( $user1, $user2 ) {
		global $userpro;
		return $userpro->upload_base_dir . $user1 . '/conversations/unread/' . $user2 . '.txt';
	}
	
	/* Get conversation archive */
	function get_conv_read( $user1, $user2 ) {
		global $userpro;
		return $userpro->upload_base_dir . $user1 . '/conversations/archive/' . $user2 . '.txt';
	}
	
	/* Format chat prior to saving to file */
	function format_chat($content, $mode) {
		
		$timestamp = current_time('timestamp');
		
		$seperator = "\n" . '[/]' . "\n";
		
		$content = trim($content);
		
		$chat = '[mode]'.$mode.'[/mode]' . "\n" . 
					'[status]unread[/status]' . "\n" . 
					'[timestamp]'.$timestamp.'[/timestamp]' . "\n" . 
					'[content]'.$content.'[/content]' 
					. $seperator;
		
		return $chat;
	}
	
	/* Write chats */
	function write_chat($user1, $user2, $content, $mode) {
		$conversation = $this->get_conv_read($user1, $user2);
		$old_content = @file_get_contents($conversation);
		$formatted = $this->format_chat($content, $mode);
		@file_put_contents( $conversation, $formatted . $old_content);
		
		if ($mode == 'inbox') {
		$conversation2 = $this->get_conv_unread($user1, $user2);
		$old_content2 = @file_get_contents($conversation2);
		$formatted2 = $this->format_chat($content, $mode);
		@file_put_contents( $conversation2, $formatted2 . $old_content2);
		}
	}
	
	/* Check if dir is empty */
	function is_dir_empty($dir) {
	  if (!is_readable($dir)) return true; 
	  $handle = opendir($dir);
	  while (false !== ($entry = readdir($handle))) {
		if ($entry != "." && $entry != "..") {
		  return false;
		}
	  }
	  return true;
	}
	
	/* Has new chats */
	function has_new_chats($user_id) {
		$unread = $this->get_conv_unread_folder($user_id);
		if (is_readable($unread) && !$this->is_dir_empty($unread)  )
			return true;
		return false;
	}
	
	/* new chats notification */
	function new_chats_notifier($user_id) {
		$unread = $this->get_conv_unread_folder($user_id);
		$num = 0;
		
		if (!$this->is_dir_empty($unread)) {
			$count = count(glob( $unread . "*.txt"));
			if ($count > 0) {
				foreach (glob( $unread . '*.txt') as $user) {
					$content = @file_get_contents($user);
					$content = explode('[/]', $content);
					$num += count($content) - 1;
				}
				
			}
		}
		
		if ($num == 1) {
			return sprintf(__('%s Unread Message','userpro-msg'), $num);
		} else {
			return sprintf(__('%s Unread Messages','userpro-msg'), $num);
		}
	}
	
	/* new messages number */
	function new_chats_notifier_count($user_id) {
		$unread = $this->get_conv_unread_folder($user_id);
		$num = 0;
		
		if (!$this->is_dir_empty($unread)) {
			$count = count(glob( $unread . "*.txt"));
			if ($count > 0) {
				foreach (glob( $unread . '*.txt') as $user) {
					$content = @file_get_contents($user);
					$content = explode('[/]', $content);
					$num += count($content) - 1;
				}
				
			}
		}
		
		return $num;
	}
	
	/* get thumbs for unread messages */
	function new_chats_user_thumbs($user_id) {
		global $userpro;
		$output = null;
		$unread = $this->get_conv_unread_folder($user_id);
		if (!$this->is_dir_empty($unread)) {
			foreach (glob( $unread . '*.txt') as $user) {
				$id = str_replace('.txt','', basename($user));
				$output .= '<a href="'.$userpro->permalink($id).'">'.get_avatar($id, 20).'</a>';
			}
		}
		return $output;
	}
	
	/* Do chat directory */
	function do_chat_dir($user1, $user2, $mode) {
		global $userpro;
		$userpro->do_uploads_dir($user1);
		if (!file_exists( $userpro->upload_base_dir . $user1 . '/conversations/archive/' )) {
			@mkdir( $userpro->upload_base_dir . $user1 . '/conversations/archive/', 0777, true);
		}
		if (!file_exists( $userpro->upload_base_dir . $user1 . '/conversations/unread/' )) {
			@mkdir( $userpro->upload_base_dir . $user1 . '/conversations/unread/', 0777, true);
		}
		// create conversation txt files
		if (isset($user) && $user !== '') {
			if ($mode == 'sent') {
				$conversation = $this->get_conv_read($user1, $user2);
			} else {
				$conversation = $this->get_conv_unread($user1, $user2);
			}
			// create empty conversation if it does not exist
			if (!file_exists( $conversation )) {
				$content = "";
				$fp = fopen( $conversation ,"wb");
				fwrite($fp,$content);
				fclose($fp);
			}
		}
	}
	
	function get_role_by_id($uid) {
		global $wp_roles;
		$user = get_user_by('id', $uid);
		$roles = $user->roles;
		$role = array_shift($roles);
		return isset($wp_roles->role_names[$role]) ? $role : false;
	}
	

}

$userpro_msg = new userpro_msg_api();

