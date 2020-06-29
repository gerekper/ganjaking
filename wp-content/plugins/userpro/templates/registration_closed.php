<div class="userpro userpro-<?php echo $i; ?> userpro-<?php echo $layout; ?>" <?php userpro_args_to_data( $args ); ?>>

	<a href="#" class="userpro-close-popup"></a>

	<div class="userpro-head">
		<div class="userpro-left"><?php echo __('Registration Closed','userpro'); ?></div>
		<?php if ($args["{$template}_side"]) { ?>
		<div class="userpro-right"><a href="#" data-template="<?php echo $args["{$template}_side_action"]; ?>"><?php echo $args["{$template}_side"]; ?></a></div>
		<?php } ?>
		<div class="userpro-clear"></div>
	</div>
	
	<div class="userpro-body">
	
		<?php do_action('userpro_pre_form_message'); ?>
		
		<p><?php _e('Registration is currently disabled. We apologize for any inconvenience.','userpro'); ?></p>

	</div>

</div>
