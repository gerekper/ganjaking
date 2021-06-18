<div class="userpro-msg-overlay-content">

	<a href="#" class="userpro-msg-close"><?php _e('Close','userpro'); ?></a>

	<div class="userpro-msg-new">

		<div class="userpro-msg-user">
			
			<div class="userpro-msg-user-thumb"><?php echo get_avatar($user_id, 50); ?></div>
			<div class="userpro-msg-user-info">
				<div class="userpro-msg-user-name">
					<a href="<?php echo $userpro->permalink($user_id); ?>"><?php echo userpro_profile_data('display_name', $user_id); ?></a><?php echo userpro_show_badges($user_id, $inline=true); ?>
				</div>
				<div class="userpro-msg-user-tab"><a href="<?php echo $userpro->permalink($user_id); ?>" class="userpro-flat-btn"><?php _e('View Profile','userpro-msg'); ?></a></div>
			</div>
			
		<div class="userpro-clear"></div>
		</div>
			
		<div class="userpro-msg-body">
			
			<?php echo $userpro_msg->load_broadcast_form( $user_id); ?>
		
		</div>
		
	</div>

</div>
