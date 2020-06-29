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
	
		<?php if (isset($users['users'])){ ?>
		<?php foreach($users['users'] as $user) : $user_id = $user->ID; ?>
		
		<div class="userpro-list-item">
		
			<?php if ($list_showthumb) { ?>
			<div class="userpro-list-item-i" data-key="profilepicture">
				<a href="<?php echo $userpro->permalink( $user_id ); ?>" class=<?php userpro_user_via_popup($args);?> data-up_username="<?php echo $userpro->id_to_member($user_id); ?>"><?php echo get_avatar( $user_id, $list_thumb ); ?></a>
			</div>
			<?php } ?>
			
			<div class="userpro-list-item-d">
				
				<a href="<?php echo $userpro->permalink( $user_id ); ?>" class="<?php userpro_user_via_popup($args);?> userpro-list-item-name" title="<?php _e('View Profile','userpro'); ?>" data-up_username="<?php echo $userpro->id_to_member($user_id); ?>"><?php echo userpro_profile_data('display_name', $user_id); ?></a><?php echo userpro_show_badges( $user_id ); ?>
				
				<?php if ($list_showbio && $userpro->shortbio($user_id)) : ?><div class="userpro-list-item-bio"><?php echo $userpro->shortbio( $user_id ); ?></div><?php endif; ?>
				
				<?php if (!isset($disable_name_hooks)){ /* display hooks after user name and badge */
				do_action('userpro_after_name_user_list', $user_id);
				} ?>
				
				<?php if ($list_showsocial) { ?>
				<div class="userpro-list-item-icons">
					<?php echo $userpro->show_social_bar( $args, $user_id ); ?>
				</div>
				<?php } ?>
				
			</div>
			
			<div class="userpro-clear"></div>
			
		</div>
		
		<?php endforeach; ?>
		<?php } ?>
	
	</div>

</div>
