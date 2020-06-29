<div class="userpro userpro-<?php echo $i; ?> userpro-<?php echo $layout; ?>" <?php userpro_args_to_data( $args ); ?>>

	<div class="userpro-head">
		<div class="userpro-left"><i class="userpro-icon-lock"></i><?php echo __('Restricted Content / Members Only','userpro'); ?></div>
		<div class="userpro-clear"></div>
	</div>
	
	<div class="userpro-body">
	
		<?php 
		
		do_action('userpro_pre_form_message'); 
		
		if($args['login_redirect']==""){
			$redirect_condition = 'force_redirect_uri="1"';
		}else{
			$redirect_condition = 'data-login_redirect="'.$args['login_redirect'].'"';
		}
	
		$builtin = array(
				'{LOGIN_POPUP}' => '<a href="#" class="popup-login" '.$redirect_condition.'>Login</a>',
				'{REGISTER_POPUP}' => '<a href="#" class="popup-register">Register</a>'
		);
		
		$search = array_keys($builtin);
		$replace = array_values($builtin);
		
		$restricted_content_text = html_entity_decode(userpro_get_option('restricted_content_text'));
		
		?><p><?php 
					echo str_replace( $search, $replace, $restricted_content_text );
		?></p>
		
	</div>
</div>