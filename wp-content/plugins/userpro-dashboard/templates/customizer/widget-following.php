<div class="updb-widget-style">
<div class="updb-user-following">
	<div class="updb-basic-info">
		<?php
			global $userpro_social, $userpro; 
			if ($userpro->is_user_logged_user($user_id)) { ?>
			<?php _e('People followed by you','userpro-dashboard'); ?>
			<?php } else { ?>
			<?php _e('People followed by '.userpro_profile_data('display_name', $user_id),'userpro-dashboard'); ?>
			<?php } ?>	
	</div>
	<?php
		$following = array();
		$template = 'following';
		$following = $userpro_social->following( $user_id );
	$i=0;
	$arrfollow=array();
	if(is_array( $following ) ){	
		$following = array_reverse($following, true); 
	}
	if( !empty( $following ) ){
 	foreach($following as $user=>$arr) : $userdata = get_userdata($user); if ($userdata) { $i++; ?>
		
		<div class="userpro-sc">
		
			<div class="userpro-sc-img" data-key="profilepicture">
				<a href="<?php echo $userpro->permalink( $user ); ?>"><?php echo get_avatar( $user, 40 ); ?></a>
			</div>
			
			<div class="userpro-sc-i">
				<div class="userpro-sc-i-name"><a href="<?php echo $userpro->permalink( $user ); ?>" title="<?php _e('View Profile','userpro'); ?>"><?php echo userpro_profile_data('display_name', $user); ?></a><?php echo userpro_show_badges( $user ); ?></div>
				<?php if ($userpro->shortbio($user)) : ?><div class="userpro-sc-i-bio"><?php echo $userpro->shortbio( $user ); ?></div><?php endif; ?>
				<div class="userpro-sc-i-icons"><?php echo userpro_profile_icons( $args, $user ); ?></div>
			</div>
			
			<div class="userpro-sc-btn">
				<?php echo $userpro_social->follow_text($user, get_current_user_id()); ?>
			</div>
			
			<div class="userpro-clear"></div>

		</div>
		
		<?php
			}
			else { /* user not found */ $userpro_social->unset_following($user_id, $user); } 
			endforeach;  
		} 
		?>
		<?php if ($i == 0) { // no members ?>
		<div class="userpro-sc userpro-sc-noborder">
			<?php if ($userpro->is_user_logged_user($user_id)) { ?>
			<?php _e('You have not started following anyone yet.','userpro'); ?>
			<?php } else { ?>
			<?php _e('This user have not started following anyone yet.','userpro'); ?>
			<?php } ?>
		</div>
		<?php } 
			?>
</div>
</div>
