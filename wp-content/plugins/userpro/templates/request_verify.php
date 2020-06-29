<div class="userpro userpro-<?php echo $i; ?> userpro-<?php echo $layout; ?>" <?php userpro_args_to_data( $args ); ?>>

	<a href="#" class="userpro-close-popup"><?php _e('Close','userpro'); ?></a>
	
	<div class="userpro-head">
		<div class="userpro-left"><?php echo __('Your request is sent!','userpro'); ?></div>
		<div class="userpro-clear"></div>
	</div>
	
	<div class="userpro-body">
	
		<?php do_action('userpro_pre_form_message'); ?>
		
		<p><?php printf(__('Thanks, <strong>%s</strong>! Your request to get verified has been sent to the staff. A staff member will look at your application shortly and take the appropriate action.','userpro'), $userpro->display_name_by_arg($_POST['up_username']) ); ?></p>

	</div>

</div>
