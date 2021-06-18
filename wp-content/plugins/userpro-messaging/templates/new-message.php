<div class="userpro-msg-overlay-content">

	<a href="#" class="userpro-msg-close"><?php _e('Close','userpro'); ?></a>

	<div class="userpro-msg-new">

		<div class="userpro-msg-user">
			
			<div class="userpro-msg-user-thumb"><?php echo get_avatar($chat_with, 50); ?></div>
			<div class="userpro-msg-user-info">
				<div class="userpro-msg-user-name">
					<a href="<?php echo $userpro->permalink($chat_with); echo 'hello';?>"><?php echo userpro_profile_data('display_name', $chat_with); ?></a><?php echo userpro_show_badges($chat_with, $inline=true); ?>
				</div>
				<div class="userpro-msg-user-tab"><a href="<?php echo $userpro->permalink($chat_with); ?>" class="userpro-flat-btn"><?php _e('View Profile','userpro-msg'); ?></a></div>
			</div>
			
		<div class="userpro-clear"></div>
		</div>
			
		<div class="userpro-msg-body">
			
			<?php echo $userpro_msg->load_chat_form( $chat_from, $chat_with ); ?>
		
		</div>
		
	</div>

</div>
