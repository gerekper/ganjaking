<?php
	$profile_privacy = userpro_profile_data('profile_privacy', $user_id);
	if( !empty($profile_privacy) && $user_id != get_current_user_id() && !current_user_can('manage_options') ){
		?>
		<div class="userpro userpro-<?php echo $i; ?> userpro-id-<?php echo $user_id; ?> userpro-<?php echo $layout; ?>" <?php userpro_args_to_data( $args ); ?>>
			<div class="userpro-head">
				<div class="userpro-left"><i class="userpro-icon-lock"></i><?php echo __('Restricted Profile','userpro'); ?></div>
				<div class="userpro-clear"></div>
			</div>
			
			<div class="userpro-body">
				<div class="userpro-clear"></div>
				<div class="profile-privacy-text" style="padding-top: 3%;"><?php echo _e("This profile is made hidden by the user","userpro");?></div>
		
			</div>
		</div>
	
		<?php
	}
	else{

	$updb_show_customizer = 0;
	$userpro_db_enable = 0;
	if( !isset( $updb_default_options ) ){
		if( class_exists( 'UPDBDefaultOptions' ) ){
			$updb_default_options = new UPDBDefaultOptions();
			$userpro_db_custom_layout = $updb_default_options->updb_get_option('userpro_db_custom_layout');
	    		$updb_show_customizer =  $updb_default_options->updb_get_option( 'show_profile_customizer' );
			$userpro_db_enable =  $updb_default_options->updb_get_option( 'userpro_db_enable' );
		}
	}
	
	if(isset($userpro_db_custom_layout) && $userpro_db_custom_layout == '1'){
		$widget_col1 = $updb_default_options->updb_get_option( 'updb_admin_widget_layout_1' );
		$widget_col2 = $updb_default_options->updb_get_option( 'updb_admin_widget_layout_2' );
		$widget_col3 = $updb_default_options->updb_get_option( 'updb_admin_widget_layout_3' );		
	}else{
		$widget_col1 = get_user_meta( $user_id, 'updb_widget_col_1', true);
		$widget_col2 = get_user_meta( $user_id, 'updb_widget_col_2', true);
		$widget_col3 = get_user_meta( $user_id, 'updb_widget_col_3', true);
	}
	
	$show_widgets = 0;
	if( (!empty( $widget_col1 ) || !empty( $widget_col2 ) || !empty( $widget_col3 )) && $updb_show_customizer && $userpro_db_enable){
		$args['header_only'] = 1;
		$show_widgets = 1;
	}
?>
<?php 
	if(isset($args['modern_layout'])){
		$modern_layout = $args['modern_layout'];
	}else{
		$modern_layout = userpro_get_option( 'up_modern_layout' );
	}	
	if( $modern_layout != 0 && !$show_widgets ){
		include userpro_path.'profile-layouts/layout-switcher.php';
		$layout_switcher = new up_layout_switcher();
		$layout_switcher->load_layout($modern_layout , $user_id, $args, $template , $i );
		wp_dequeue_style('userpro_min');
		wp_dequeue_style('userpro_skin_min');
	}else{
?>

<div class="userpro userpro-<?php echo $i; ?> userpro-id-<?php echo $user_id; ?> userpro-<?php echo $layout; ?> <?php echo $show_widgets?'userpro-updb-widgets':'';?>" <?php userpro_args_to_data( $args ); ?>>

	<a href="#" class="userpro-close-popup"><?php _e('Close','userpro'); ?></a>
	
	<div class="userpro-centered <?php if (isset($args['header_only']) && $args['header_only']) { echo 'userpro-centered-header-only'; } ?>">
	
		<?php if ( userpro_get_option('lightbox') && userpro_get_option('profile_lightbox') ) { ?>
		<div class="userpro-profile-img" data-key="profilepicture"><a href="<?php echo $userpro->profile_photo_url($user_id); ?>" class="userpro-tip-fade lightview" data-lightview-caption="<?php echo $userpro->profile_photo_title( $user_id ); ?>" title="<?php _e('View member photo','userpro'); ?>">

                <img src="<?php echo $userpro->profile_photo_url($user_id); ?>" /></a></div>
		<?php } else { ?>
		<div class="userpro-profile-img" data-key="profilepicture"><a href="<?php echo $userpro->permalink($user_id); ?>" title="<?php _e('View Profile','userpro'); ?>"><?php echo get_avatar( $user_id, $profile_thumb_size ); ?></a></div>
		<?php } ?>
		<div class="userpro-profile-img-after">
			<div class="userpro-profile-name">
				<a href="<?php echo $userpro->permalink($user_id); ?>"><?php echo userpro_profile_data('display_name', $user_id); ?></a><?php 
			if(userpro_get_option('show_badges_profile')=='1')
			echo userpro_show_badges( $user_id );
			else
			{?>
			<?php 
		  if(userpro_show_badges( $user_id )!='<span class="userpro-badges"></span>')	{?>		
		
			<span class="badges"></span>
			<i  onclick="userpro_show_user_badges(<?php echo $user_id;?>);" class="fa fa-arrow-circle-right display_badges"></i>
			 <?php }}?>
			</div>
			<?php do_action('userpro_after_profile_img' , $user_id); 

		
		?>
			<?php if ( userpro_can_edit_user( $user_id ) || userpro_get_edit_userrole() ) { 
			
			?>
			<div class="userpro-profile-img-btn">
				<?php if (isset($args['header_only']) && $args['header_only']){ ?>
				<a href="<?php echo $userpro->permalink($user_id, 'edit'); ?>" class="userpro-button secondary"><?php _e('Edit Profile','userpro') ?></a>
				<?php } else { ?>
				<a href="#" data-up_username="<?php echo $userpro->id_to_member($user_id); ?>" data-template="edit" class="userpro-button secondary"><?php _e('Edit Profile','userpro'); ?></a>
				<?php } ?>
				<img src="<?php echo $userpro->skin_url(); ?>loading.gif" alt="" class="userpro-loading" />
			</div>
			<?php } ?>
		</div>
		
		<div class="userpro-profile-icons top">
			<?php if (isset($args['permalink'])) {
				userpro_logout_link( $user_id, $args['permalink'], $args['logout_redirect'] );
			} else {
				userpro_logout_link( $user_id );
			} ?>
		</div>
			
		<?php echo $userpro->show_social_bar( $args, $user_id, 'userpro-centered-icons' ); ?>

		<div class="userpro-clear"></div>
			
	</div>
	<?php
		if($show_widgets){
			$hook_args = array_merge($args, array('user_id' => $user_id, 'unique_id' => $i));
			do_action('userpro_after_profile_head', $hook_args);
		}
	?>
	<?php if (!isset($args['header_only'] ) || (isset($args['header_only']) && ! $args['header_only'] )) { ?>
	
	<?php
	// action hook after user header
	if (!isset($args['disable_head_hooks'])){
		if (!isset($user_id)) $user_id = 0;
		$hook_args = array_merge($args, array('user_id' => $user_id, 'unique_id' => $i));
		do_action('userpro_after_profile_head', $hook_args);
	}
	?>
	
	<div class="userpro-body">
	
		<?php do_action('userpro_pre_form_message'); ?>

		<form action="" method="post" data-action="<?php echo $template; ?>">
		
			<input type="hidden" name="user_id-<?php echo $i; ?>" id="user_id-<?php echo $i; ?>" value="<?php echo $user_id; ?>" />
			
			<?php // Hook into fields $args, $user_id
			if (!isset($user_id)) $user_id = 0;
			$hook_args = array_merge($args, array('user_id' => $user_id, 'unique_id' => $i));
			do_action('userpro_before_fields', $hook_args);
			?>
			
			<?php foreach( userpro_fields_group_by_template( $template, $args["{$template}_group"] ) as $key => $array ) { ?>
				
				<?php  if ($array) echo userpro_show_field( $key, $array, $i, $args, 0, $user_id ) ?>
				
			<?php } ?>
			
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
			
			<?php if ( userpro_can_delete_user($user_id) || $userpro->request_verification($user_id) || isset( $args["{$template}_button_primary"] ) || isset( $args["{$template}_button_secondary"] ) ) { ?>
			<div class="userpro-field userpro-submit userpro-column">
				
				<?php if ( $userpro->request_verification($user_id) ) { ?>
				<input type="button" value="<?php _e('Request Verification','userpro'); ?>" class="popup-request_verify userpro-button secondary" data-up_username="<?php echo $userpro->id_to_member($user_id); ?>" />
				<?php } ?>
				
				<?php if ( userpro_can_delete_user($user_id) ) { ?>
				<input type="button" value="<?php _e('Delete Profile','userpro'); ?>" class="userpro-button red" data-template="delete" data-up_username="<?php echo $userpro->id_to_member($user_id); ?>" />
				<?php } ?>
				
				<?php if (isset($args["{$template}_button_primary"]) ) { ?>
				<input type="submit" value="<?php echo $args["{$template}_button_primary"]; ?>" class="userpro-button" />
				<?php } ?>
				
				<?php if (isset( $args["{$template}_button_secondary"] )) { ?>
				<input type="button" value="<?php echo $args["{$template}_button_secondary"]; ?>" class="userpro-button secondary" data-template="<?php echo $args["{$template}_button_action"]; ?>" />
				<?php } ?>

				<img src="<?php echo $userpro->skin_url(); ?>loading.gif" alt="" class="userpro-loading" />
				<div class="userpro-clear"></div>
				
			</div>
			<?php } ?>
		
		</form>
	
	</div>
	
	<?php } ?>

</div>
<?php }?>
<?php
	if( $show_widgets ){
		do_action( 'after_userpro_profile_div', $args, $user_id, $i );
	}	
	}
?>