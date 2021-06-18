<?php if( get_current_user_id() != $user_id ){?>
	<div class="updb-widget-style">
		<div class="updb-basic-info"><?php _e( 'Send a message', 'userpro-msg' );?></div>
		<div class="updb-view-profile-details"><br>
			<?php
				global $userpro_msg;
				
				$chat_from = get_current_user_id();
				$chat_with = $user_id;
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
				
				
					$output = '<form action="" method="post" class="userpro-send-chat-widget">
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
				echo $output;
				
		?>
		<br>
		<input type="button" class="userpro-button secondary userpro-tip" style="" name="send-message" value="<?php echo __('Send Message','userpro-msg');?>" id="send-message" />	
		</div>
	</div>
<?php }?>