<div class="userpro userpro-<?php echo $i; ?> userpro-<?php echo $layout; ?>" <?php userpro_args_to_data( $args ); ?>>

	<div class="userpro-head">
		<div class="userpro-left"><i class="userpro-icon-lock"></i><?php echo __('Access Denied','userpro'); ?></div>
		<div class="userpro-clear"></div>
	</div>
	
	<div class="userpro-body">
	
		<?php do_action('userpro_pre_form_message'); ?>
		
		<p><?php echo __('You do not have enough permissions or role to view this private content. We apologize for that.','userpro'); ?></p>

	</div>

</div>