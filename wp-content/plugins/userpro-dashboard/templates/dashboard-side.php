<div class="LeftDiv">
	<div class="userpro-dash">
		<div class="userpro-dashboard-leftsidebar">
			<div class="myUser" style="background:#fff">
				<div class="picUser">
					<?php if ( userpro_get_option('lightbox') && userpro_get_option('profile_lightbox') ) { ?>
					<div class="userpro-profile-img" data-key="profilepicture"><a href="<?php echo $userpro->profile_photo_url($user_id); ?>" class="userpro-tip-fade lightview" data-lightview-caption="<?php echo $userpro->profile_photo_title( $user_id ); ?>" title="<?php _e('View member photo','userpro'); ?>"><?php echo get_avatar( $user_id, $args['profile_thumb_size'] ); ?></a></div>
		<?php } else { ?>
		<div class="userpro-profile-img" data-key="profilepicture"><a href="<?php echo $userpro->permalink($user_id); ?>" title="<?php _e('View Profile','userpro'); ?>"><?php echo get_avatar( $user_id, $args['profile_thumb_size'] ); ?></a></div>
		<?php } ?>
				</div><!-- picuser ends-->
				
				<div class="uploadPic" >
					<a href="<?php echo $userpro->permalink($user_id);?>" class="uploadPic-box">
						<span>
							<i class="fa fa-user"></i>
						</span>
						<?php _e( 'See Public Profile', 'userpro-dashboard' );?>
					</a>
					</div>
					
					<div class="uploadPic dashboard-side" data-id = "dashboard-upload-pic">
					<a href="#" class="uploadPic-box">
						<span>
							<i class="fa fa-camera"></i>
						</span>
						<?php _e('Upload Profile Picture', 'userpro-dashboard' );?>
					</a>
					</div>
					
					<?php if( isset( $edit_fields['custom_profile_bg']) ){?>
					<div class="uploadPic dashboard-side" data-id = "dashboard-bg-upload-pic">
					<a href="#" class="uploadPic-box">
						<span>
							<i class="fa fa-picture-o"></i>
						</span>
						<?php _e('Upload Profile Background', 'userpro-dashboard');?>
					</a>
					</div>
					<?php }?>
					<div class="uploadPic dashboard-side" data-id = "dashboard-profile">
					<a href="#" class="uploadPic-box">
						<span>
							<i class="fa fa-user"></i>
						</span>
						<span class="labelName"><?php _e( 'My Profile', 'userpro-dashboard' );?></span>
					</a>
					</div>
					<?php if( isset( $edit_fields['user_pass'] ) && isset( $edit_fields['user_pass_confirm'] ) && isset( $edit_fields['passwordstrength'] ) ){ ?>
					<div class="uploadPic dashboard-side" data-id="dashboard-pass">
					<a href="#" class="uploadPic-box">
						<span>
							<i class="fa fa-cog"></i>
						</span>
						<?php _e('Setting', 'userpro-dashboard' );?>
					</a>
					</div>
					<?php } 
						if($updb_default_options->updb_get_option('show_profile_customizer') == '1' && $updb_default_options->updb_get_option('userpro_db_custom_layout') == '0'){
					?>
					
					<div class="uploadPic dashboard-side" data-id = "dashboard-profile-customizer">
					<a href="#" class="uploadPic-box">
						<span>
							<i class="fa fa-user"></i>
						</span>
						<span class="labelName"><?php _e( 'Profile Customizer', 'userpro-dashboard' );?></span>
					</a>
					</div>	
					<?php $updb_post_enable = $updb_default_options->updb_get_option( 'userpro_db_post_enable' );
                                            if(isset($updb_post_enable) && $updb_post_enable == 1) {
                                        ?>
                                        <div class="uploadPic dashboard-side" data-id = "dashboard-my-posts">
					<a href="#" class="uploadPic-box">
						<span>
							<i class="fa fa-tags"></i>
						</span>
						<span class="labelName"><?php _e( 'My Posts', 'userpro-dashboard' );?></span>
					</a>
					</div>	
                                        <?php
                                            }
                                            }
						do_action( 'after_dashboard_side' );
					?>
					<div class="uploadPic" id="userpro-dash-logout">
					<div class="uploadPic-box">
						<span>
							<i class="fa fa-sign-out">
							</i>
						</span>
						<?php if (isset($args['permalink'])) {
							userpro_logout_link( $user_id, $args['permalink'], $args['logout_redirect'] );
						} else {
							userpro_logout_link( $user_id );
						} ?>
					</div>
					</div>
				</div>
			
			</div>
			
			
			
			
		</div><!--userpro-dashboard-leftsidebar-->

</div>
