<div class="userpro userpro-<?php echo $i; ?> userpro-<?php echo $layout; ?>" <?php userpro_args_to_data( $args ); ?>>

	<a href="#" class="userpro-close-popup"><?php _e('Close','userpro'); ?></a>
	
	<div class="userpro-head">
		<div class="userpro-left"><?php echo $args["{$template}_heading"]; ?></div>
		<?php if (isset($args["{$template}_side"])) { ?>
		<div class="userpro-right"><a href="#" data-template="<?php echo $args["{$template}_side_action"]; ?>"><?php echo $args["{$template}_side"]; ?></a></div>
		<?php } ?>
		<div class="userpro-clear"></div>
	</div>
	
	<div class="userpro-body">
	
		<?php if (isset($users)){ ?>
		
		<div class="userpro-online-count"><?php echo $userpro->online_users_count( count($users) ); ?></div>
		
		<?php foreach($users as $user) : $user_id = $user->ID; ?>
		
		<?php if ($args['online_mode'] == 'vertical') { ?>
		<div class="userpro-online-item">
		
			<?php if ($args['online_showthumb']) { ?>
			<div class="userpro-online-item-i" data-key="profilepicture">
				<a href="<?php echo $userpro->permalink( $user_id ); ?>"><?php echo get_avatar( $user_id, $args['online_thumb'] ); ?></a>
			</div>
			<?php } ?>
			
			<div class="userpro-online-item-d">
				
				<a href="<?php echo $userpro->permalink( $user_id ); ?>" class="userpro-online-item-name <?php echo $userpro->online_user_special($user_id); ?>" title="<?php _e('View Profile','userpro'); ?>"><?php echo userpro_profile_data('display_name', $user_id); ?></a><?php echo userpro_show_badges( $user_id ); ?>
				
				<?php if ($args['online_showbio'] && $userpro->shortbio($user_id)) : ?><div class="userpro-online-item-bio"><?php echo $userpro->shortbio( $user_id ); ?></div><?php endif; ?>
				
				<?php if (!isset($args['disable_name_hooks'])){ /* display hooks after user name and badge */
				do_action('userpro_after_name_user_list', $user_id);
				} ?>
				
				<?php if ($args['online_showsocial']) { ?>
				<div class="userpro-online-item-icons">
					<?php echo $userpro->show_social_bar( $args, $user_id ); ?>
				</div>
				<?php } ?>
				
			</div>
			
			<div class="userpro-clear"></div>
			
		</div>
		<?php } else { ?>
		
		<div class="userpro-online-i">
			<?php if ($args['online_showthumb']) { ?><a href="<?php echo $userpro->permalink( $user_id ); ?>" class="userpro-online-i-thumb"><?php echo get_avatar( $user_id, $args['online_thumb'] ); ?></a><?php } ?>
			<a href="<?php echo $userpro->permalink( $user_id ); ?>" class="userpro-online-i-name <?php echo $userpro->online_user_special($user_id); ?>" title="<?php _e('View Profile','userpro'); ?>"><?php echo userpro_profile_data('display_name', $user_id); ?></a>
		</div>
		
		<?php } ?>
		
		<?php endforeach; ?>
		<?php } else { ?>
		
		<div class="userpro-online-item">
			<div class="userpro-online-item-d"><?php _e('No users are online now.','userpro'); ?></div>
			<div class="userpro-clear"></div>
		</div>
		
		<?php } ?>
	
	</div>

</div>
