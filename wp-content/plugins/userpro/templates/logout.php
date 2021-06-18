 <a href="#" class="userpro-close-popup"><?php _e('Close','userpro'); ?></a>
<div class="userpro userpro-<?php echo $i; ?> userpro-id-<?php echo $user_id; ?> userpro-<?php echo $layout; ?>" <?php userpro_args_to_data( $args ); ?>>


	
	<div class="userpro-head userpro-centered-header-only userpro-is-responsive">
	
		<div class="userpro-left">
		
			<div class="userpro-profile-img" data-key="profilepicture"><a href="<?php echo $userpro->permalink(); ?>"><?php echo get_avatar( $user_id, 64 ); ?></a></div>

			<div class="userpro-profile-img-after">
				<div class="userpro-profile-name">
					<a href="<?php echo $userpro->permalink(); ?>" title="<?php _e('View/manage your profile','userpro'); ?>"><?php echo userpro_profile_data('display_name', $user_id); ?></a><?php echo userpro_show_badges( $user_id ); ?>
				</div>
				<div class="userpro-profile-img-btn">
					<a href="<?php echo $userpro->permalink(); ?>" class="userpro-button secondary"><?php _e('View Profile','userpro'); ?></a>
					<a href="<?php echo userpro_logout_url( $user_id, $args['permalink'], $args['logout_redirect'] ); ?>" class="userpro-button secondary"><?php _e('Logout','userpro'); ?></a>
				</div>
			</div>
			
		</div>
			
		<div class="userpro-clear"></div>
			
	</div>

</div>
