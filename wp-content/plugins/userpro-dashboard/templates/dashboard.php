<div class="profileDashboard dashboardRight" id = "dashboard-profile" style="display:block;">
	<div class="userpro-dashboard userpro-<?php echo $i; ?> userpro-id-<?php echo $user_id; ?> userpro-<?php echo $args['layout']; ?>" <?php userpro_args_to_data( $args ); ?>>
				<form action="" method="post" data-action="<?php echo 'edit'; ?>">
				<div class="profileForm">
					<input type="hidden" name="user_id-<?php echo $i; ?>" id="user_id-<?php echo $i; ?>" value="<?php echo $user_id; ?>" />
					
				<?php 
					$hook_args = array_merge($args, array('user_id' => $user_id, 'unique_id' => $i));
					do_action('userpro_before_fields', $hook_args);
					foreach( $edit_fields as $key => $array ) { 
					if ($array) {
						if( $key == 'profilepicture' || $key == 'custom_profile_bg' || $key == 'accountinfo' || $key == 'user_email' || $key == 'user_pass' || $key == 'user_pass_confirm' || $key == 'passwordstrength' )	{
							continue;
						}						
						echo userpro_edit_field( $key, $array, $i, $args, $user_id );
						}
				
					 } ?>
			<?php if ( userpro_can_delete_user($user_id) || $userpro->request_verification($user_id) || isset( $args["edit_button_primary"] ) || isset( $args["edit_button_secondary"] ) ) { ?>
			<div class="userpro-field userpro-submit userpro-column">
				
				<?php if (isset($args["edit_button_primary"]) ) { ?>
				<input type="submit" value="<?php echo $args["edit_button_primary"]; ?>" class="userpro-button" />
				<?php } ?>
				
				<?php if (isset( $args["edit_button_secondary"] )) { ?>
				<input type="button" value="<?php echo $args['edit_button_secondary']; ?>" class="userpro-button secondary" data-template="<?php echo $args['edit_button_action']; ?>" />
				<?php } ?>
				
				<?php if ( $userpro->request_verification($user_id) ) { ?>
				<input type="button" value="<?php _e('Request Verification','userpro'); ?>" class="popup-request_verify userpro-button secondary" data-up_username="<?php echo $userpro->id_to_member($user_id); ?>" />
				<?php } ?>
				
				<?php if ( userpro_can_delete_user($user_id) ) { ?>
				<input type="button" value="<?php _e('Delete Profile','userpro'); ?>" class="userpro-button red" data-template="delete" data-up_username="<?php echo $userpro->id_to_member($user_id); ?>" />
				<?php } ?>

				<img src="<?php echo $userpro->skin_url(); ?>loading.gif" alt="" class="userpro-loading" />
				<div class="userpro-clear"></div>
				
			</div>
			<?php } ?>
			
			</div><!-- Profile dashboard ends-->
                        </form>

	</div>
<div class="clear" style="clear:both"></div>
</div>
	<?php do_action( 'after_dashboard_profile_content', $args, $edit_fields, $i ); 
		  include_once UPDB_PATH.'templates/dashboard-profile-pic.php';
	
		 if( isset( $edit_fields['custom_profile_bg'] ) ){
		  include_once UPDB_PATH.'templates/dashboard-bg-profile-pic.php';
   		}

		if( isset( $edit_fields['user_pass'] ) && isset( $edit_fields['user_pass_confirm'] ) && isset( $edit_fields['passwordstrength'] ) ){
			$pass_arr = array( 'user_pass', 'user_pass_confirm', 'passwordstrength' );
			$pass_arr_size = count( $pass_arr );
			include_once UPDB_PATH.'templates/dashboard-pass.php';
		}	
		
		if($updb_default_options->updb_get_option('show_profile_customizer')){
			include_once UPDB_PATH.'templates/customizer/profile-customizer.php';
		}
                include_once UPDB_PATH.'templates/dashboard-my-posts.php';
	?>
