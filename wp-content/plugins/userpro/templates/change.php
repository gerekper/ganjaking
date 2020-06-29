<?php
	if( userpro_get_option( 'enable_reset_by_mail' ) == 'y' && (isset($_GET['a']) && $_GET['a'] == 'reset' ) ){
		$args['template'] = 'change';
	}
?>
<div class="userpro userpro-<?php echo $i; ?> userpro-<?php echo $layout; ?>" <?php userpro_args_to_data( $args ); ?>>

	<a href="#" class="userpro-close-popup"><?php _e('Close','userpro'); ?></a>
	
	<div class="userpro-head">
		<div class="userpro-left"><?php echo $args["{$template}_heading"]; ?></div>
		<?php if ($args["{$template}_side"] && userpro_get_option('enable_reset_by_mail') == 'n' ) { ?>
		<div class="userpro-right"><a href="#" data-template="<?php echo $args["{$template}_side_action"]; ?>"><?php echo $args["{$template}_side"]; ?></a></div>
		<?php } ?>
		<div class="userpro-clear"></div>
	</div>
	
	<div class="userpro-body">
	
		<?php do_action('userpro_pre_form_message'); ?>

		<form action="" method="post" data-action="<?php echo $template; ?>">
		
			<?php
			// Hook into fields $args, $user_id
			if (!isset($user_id)) $user_id = 0;
			$hook_args = array_merge($args, array('user_id' => $user_id, 'unique_id' => $i));
			do_action('userpro_before_fields', $hook_args);
			?>
		
			<!-- fields -->
			<?php if(userpro_get_option( 'enable_reset_by_mail' ) == 'n'){ ?>
			<div class='userpro-field' data-key='secretkey'>
				<div class='userpro-label <?php if ($args['field_icons'] == 1) { echo 'iconed'; } ?>'><label for='secretkey-<?php echo $i; ?>'><?php _e('Your Secret Key','userpro'); ?></label></div>
				<div class='userpro-input'>
					<input type="text" name="secretkey-<?php echo $i; ?>" id="secretkey-<?php echo $i; ?>" data-required="1" data-ajaxcheck="validatesecretkey" />
					<div class='userpro-help'><?php _e('You need a secret key to change your account password. Do not have one? Click <a href="#" data-template="reset">here</a> to obtain a new key.','userpro'); ?></div>
					<div class='userpro-clear'></div>
				</div>
			</div><div class='userpro-clear'></div>
			<?php } else{ 
				$texttype = 'hidden';
				$sk = isset($_GET['sk']) ? $_GET['sk']: '';
				$skrequired = 0;
			?>
			
			<input type="<?php echo $texttype ?>" name="secretkey-<?php echo $i; ?>" id="secretkey-<?php echo $i; ?>" data-required="<?php echo $skrequired ?>" data-ajaxcheck="validatesecretkey" value="<?php echo $sk ?>"/>			
			<?php }?>
			<?php foreach( userpro_get_fields( array('user_pass','user_pass_confirm','passwordstrength') ) as $key => $array ) { ?>
			
				<?php $array = $userpro->fields[$key];?>
				
				<?php  if ($array) echo userpro_edit_field( $key, $array, $i, $args ) ?>
				
			<?php } ?>
			
			<?php  $key = 'antispam'; $array = $userpro->fields[$key];
				if (isset($array) && is_array($array)) echo userpro_edit_field( $key, $array, $i, $args ); ?>
			
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
			
			<?php if ($args["{$template}_button_primary"] ||  $args["{$template}_button_secondary"] ) { ?>
			<div class="userpro-field userpro-submit userpro-column">
				
				<?php if ($args["{$template}_button_primary"]) { ?>
				<input type="submit" value="<?php echo $args["{$template}_button_primary"]; ?>" class="userpro-button" />
				<?php } ?>
				
				<?php if ($args["{$template}_button_secondary"] && userpro_get_option('enable_reset_by_mail') == 'n') { ?>
				<input type="button" value="<?php echo $args["{$template}_button_secondary"]; ?>" class="userpro-button secondary" data-template="<?php echo $args["{$template}_button_action"]; ?>" />
				<?php } ?>

				<img src="<?php echo $userpro->skin_url(); ?>loading.gif" alt="" class="userpro-loading" />
				<div class="userpro-clear"></div>
				
			</div>
			<?php } ?>
		
		</form>
	
	</div>

</div>
