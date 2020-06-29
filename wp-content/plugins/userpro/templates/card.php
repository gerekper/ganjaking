<div class="userpro userpro-nostyle userpro-<?php echo $i; ?> userpro-<?php echo $layout; ?>" <?php userpro_args_to_data( $args ); ?>>

	<a href="#" class="userpro-close-popup"><?php _e('Close','userpro'); ?></a>
	
	<div class="userpro-card">
		
		<div class="userpro-card-img">
			<a href="<?php echo $userpro->permalink( $user_id ); ?>"><?php echo get_avatar( $user_id, $card_img_width ); ?></a>
		</div>
		
		<div class="userpro-card-info">
			<div class="userpro-card-left"><a href="<?php echo $userpro->permalink( $user_id ); ?>" title="<?php _e('View Full Profile','userpro'); ?>"><?php echo userpro_profile_data('display_name', $user_id); ?></a></div>
			<div class="userpro-card-right"><?php echo userpro_show_badges( $user_id, true ); ?></div>
			<div class="userpro-clear"></div>
		</div>
		
		<?php if ($card_showbio && $userpro->shortbio($user_id)) : ?><div class="userpro-card-bio"><?php echo $userpro->shortbio( $user_id ); ?></div><?php endif; ?>
		
		<?php if (!isset($disable_name_hooks)){ /* display hooks after user name and badge */
		do_action('userpro_after_name_user_list', $user_id);
		} ?>
		
		<?php if ($card_showsocial) { ?>
		<div class="userpro-card-icons"><?php echo $userpro->show_social_bar( $args, $user_id ); ?></div>
		<?php } ?>
		
		<div class="userpro-clear"></div>
		
	</div>

</div>
