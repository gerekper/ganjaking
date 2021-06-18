<div class="userpro-msg-user">
			
				<div class="userpro-msg-user-thumb"><?php echo get_avatar(get_current_user_id(), 50); ?></div>
				<div class="userpro-msg-user-info">
					<div class="userpro-msg-user-name">
						<a href="<?php echo $userpro->permalink(get_current_user_id()); ?>"><?php echo userpro_profile_data('display_name', get_current_user_id()); ?></a><?php echo userpro_show_badges(get_current_user_id(), $inline=true); ?>
					</div>
					<div class="userpro-msg-user-tab"><i class="userpro-icon-comment-alt"></i><?php _e('Message History','userpro-msg'); ?></div>
				</div>
				
				<a href="#" class="userpro-back-to-inbox" data-user_id="<?php echo get_current_user_id(); ?>"><i class="userpro-icon-angle-left"></i><?php _e('Back','userpro-msg'); ?></a>
			
			<div class="userpro-clear"></div>
			</div>
			
			<div class="userpro-msg-body alt">
			
				<?php echo $userpro_msg->conversations( get_current_user_id() ); ?>
				
				<?php echo $userpro_msg->load_chat_form( get_current_user_id(), 0 ); ?>
		
			</div>
