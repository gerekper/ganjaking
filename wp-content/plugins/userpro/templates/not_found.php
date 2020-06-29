<div class="userpro userpro-<?php echo $i; ?> userpro-<?php echo $layout; ?>" <?php userpro_args_to_data( $args ); ?>>

	<div class="userpro-head">
		<div class="userpro-left"><?php echo $args["error_heading"]; ?></div>
		<div class="userpro-clear"></div>
	</div>
	
	<div class="userpro-body">
	
		<?php do_action('userpro_pre_form_message'); ?>
		
		<p><?php _e('The user profile you are trying to reach does not exist. It could have been removed.','userpro'); ?></p>

	</div>

</div>