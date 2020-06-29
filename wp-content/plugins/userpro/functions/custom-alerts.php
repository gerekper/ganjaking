<?php

	// add custom message to a user profile
	add_action('userpro_after_profile_head', 'userpro_custom_user_alert', 10);
	function userpro_custom_user_alert($args){
		global $userpro;
		$user_id = $args['user_id'];
		if ( $user_id && in_array( $args['template'], array('view','edit') ) && !isset($args['no_style'] ) ){
		
		$alert = $userpro->get('userpro_alert', $user_id);
				
		if ($userpro->admin_user_notice( $user_id )){
		?>
		
		<div class="userpro-alert" data-user_id="<?php echo $user_id; ?>">
			
			<?php if ($alert != '') { ?>
			
				<div class="userpro-alert-content"><?php echo $alert; ?></div>
				
				<div class="userpro-alert-input" style="display:none">
					<div class="userpro-input">
						<input type="text" name="" id="" value="<?php echo $alert; ?>" placeholder="<?php _e('Set custom note...','userpro'); ?>" />
						<input type="button" value="<?php _e('Save','userpro'); ?>" class="userpro-button secondary" />
					</div>
				</div>
			
			<?php } else { ?>
			
				<div class="userpro-alert-input">
					<div class="userpro-input">
						<input type="text" name="" id="" value="" placeholder="<?php _e('Set custom note...','userpro'); ?>" />
						<input type="button" value="<?php _e('Save','userpro'); ?>" class="userpro-button secondary" />
					</div>
				</div>
			
			<?php } ?>
			
			<a href="#" class="userpro-alert-edit"><i class="userpro-icon-edit"></i></a>
			<a href="#" class="userpro-alert-close"><i class="userpro-icon-remove"></i></a>
		</div>
		
		<?php } elseif (userpro_get_option('admin_user_notices') && $alert && $user_id == get_current_user_id() && userpro_get_option('show_user_notices_him')) { ?>

			<div class="userpro-alert" data-user_id="<?php echo $user_id; ?>">
				<div class="userpro-alert-content"><?php echo $alert; ?></div>
				<a href="#" class="userpro-alert-close"><i class="userpro-icon-remove"></i></a>
			</div>
		
		<?php } elseif (userpro_get_option('admin_user_notices') && $alert && $userpro->user_notice_viewable( $user_id )) { ?>

			<div class="userpro-alert" data-user_id="<?php echo $user_id; ?>">
				<div class="userpro-alert-content"><?php echo $alert; ?></div>
				<a href="#" class="userpro-alert-close"><i class="userpro-icon-remove"></i></a>
			</div>
		
		<?php
			}
		}
	}