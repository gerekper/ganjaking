<div class="socialwall-overlay-content">

	<a href="#" class="socialwall-notify-close"><?php _e('Close','userpro'); ?></a>

	
	
			<div class="social-notify-user">
				<div class="social-notify-user-thumb"><?php echo get_avatar($user_id, 50); ?></div>
				<div class="social-notify-user-info">
				<div class="social-notify-user-name">
						<a href="<?php echo $userpro->permalink($user_id); ?>"><?php echo userpro_profile_data('display_name', $user_id); ?></a><?php echo userpro_show_badges($user_id, $inline=true); ?>
				</div>
				</div>
				<div class="userpro-clear"></div>
			</div>	
			<div class="social-notify-body alt">
			
				<?php echo display_notification($user_id); ?>
			
			</div>
		
</div>
