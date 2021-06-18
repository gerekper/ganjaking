<div class="userpro userpro-<?php echo $i; ?> userpro-<?php echo $layout; ?>" <?php userpro_args_to_data( $args ); ?>>

	<a href="#" class="userpro-close-popup"><?php _e('Close','userpro'); ?></a>
	
	<div class="userpro-head">
		<div class="userpro-left"><?php echo $args["{$template}_heading"]; ?></div>
		<?php if ($args["{$template}_side"]) { ?>
		<div class="userpro-right"><a href="#" data-template="<?php echo $args["{$template}_side_action"]; ?>"><?php echo $args["{$template}_side"]; ?></a></div>
		<?php } ?>
		<div class="userpro-clear"></div>
	</div>
	
	<div class="userpro-body">
	
		<?php do_action('userpro_pre_form_message'); ?>

		<form action="" method="post" data-action="<?php echo $template; ?>">
					
			<input type="hidden" name="user_id-<?php echo $i; ?>" id="user_id-<?php echo $i; ?>" value="<?php echo $user_id; ?>" />
		
			<?php // Hook into fields $args, $user_id
			if (!isset($user_id)) $user_id = 0;
			$hook_args = array_merge($args, array('user_id' => $user_id, 'unique_id' => $i));
			do_action('userpro_before_fields', $hook_args);
			?>
			
			<!-- fields -->
			<div class='userpro-field' data-key='confirmdelete'>
				<div class='userpro-label'><label for='confirmdelete-<?php echo $i; ?>'><?php _e('Confirm Deletion','userpro'); ?></label></div>
				<div class='userpro-input'><label class='userpro-radio full'><span class="checked"></span><input type='radio' value='0' name='confirmdelete-<?php echo $i; ?>' checked='checked' /><?php _e('No, do not delete','userpro'); ?></label><label class='userpro-radio full'><span></span><input type='radio' value='1' name='confirmdelete-<?php echo $i; ?>' /><?php _e('Yes, delete this profile!','userpro'); ?></label>
				<div class='userpro-help'><?php _e('This action cannot be UNDONE! It will delete all profile data.','userpro'); ?></div>
				<div class='userpro-clear'></div>
				</div>
			</div><div class='userpro-clear'></div>
			
			<?php // Hook into fields $args, $user_id
			if (!isset($user_id)) $user_id = 0;
			$hook_args = array_merge($args, array('user_id' => $user_id, 'unique_id' => $i));
			do_action('userpro_after_fields', $hook_args);
			?>
			
			<?php // Hook into fields $args, $user_id
			if (!isset($user_id)) $user_id = 0;
			$hook_args = array_merge($args, array('user_id' => $user_id, 'unique_id' => $i));
			do_action('userpro_before_form_submit', $hook_args);
			?>
			<!-- fields done -->
			
			<?php if (isset($args["{$template}_button_primary"]) ||  isset($args["{$template}_button_secondary"]) ) { ?>
			<div class="userpro-field userpro-submit userpro-column">
				
				<?php if (isset($args["{$template}_button_primary"])) { ?>
				<input type="submit" value="<?php echo $args["{$template}_button_primary"]; ?>" class="userpro-button" />
				<?php } ?>
				
				<?php if (isset($args["{$template}_button_secondary"])) { ?>
				<input type="button" value="<?php echo $args["{$template}_button_secondary"]; ?>" class="userpro-button secondary" data-template="<?php echo $args["{$template}_button_action"]; ?>" data-up_username="<?php echo $userpro->id_to_member($user_id); ?>" />
				<?php } ?>

				<img src="<?php echo $userpro->skin_url(); ?>loading.gif" alt="" class="userpro-loading" />
				<div class="userpro-clear"></div>
				
			</div>
			<?php } ?>
		
		</form>
	
	</div>

</div>
