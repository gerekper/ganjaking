<div class="userpro userpro-<?php echo $i; ?> userpro-id-<?php echo $user_id; ?> userpro-<?php echo $layout; ?>" <?php userpro_args_to_data( $args ); ?>>

	<a href="#" class="userpro-close-popup"><?php _e('Close','userpro'); ?></a>
	
	<div class="userpro-centered <?php if (isset($args['header_only'])) { echo 'userpro-centered-header-only'; } ?>">
	
		<div class="userpro-profile-img" data-key="profilepicture"><a href="<?php echo $userpro->permalink($user_id); ?>"><?php echo get_avatar( $user_id, 80 ); ?></a></div>

		<div class="userpro-profile-img-after">
			<div class="userpro-profile-name">
				<a href="<?php echo $userpro->permalink($user_id); ?>"><?php echo userpro_profile_data('display_name', $user_id); ?></a><?php echo userpro_show_badges( $user_id ); ?>
			</div>
			<?php if ( userpro_can_edit_user( $user_id ) ) { ?>
			<div class="userpro-profile-img-btn">
				<a href="<?php echo $userpro->permalink($user_id); ?>" class="userpro-button secondary"><?php _e('View Profile','userpro') ?></a>
				<img src="<?php echo userpro_url; ?>skins/<?php echo $args['skin']; ?>/img/loading.gif" alt="" class="userpro-loading" />
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
	
	<?php $j = 0; if (isset($user_request) && $user_request != '' ) : ?>
			
	<?php $user_request = array_reverse($user_request, true); foreach($user_request as $user=>$arr) : $userdata = get_userdata($user); if ($userdata) { $j++; ?>
		
		<?php if($user_id == get_current_user_id()){?>
			<div class="userpro-sc" id="<?php echo $user; ?>">
			
				<div class="userpro-sc-img" data-key="profilepicture">
					<a href="<?php echo $userpro->permalink( $user ); ?>"><?php echo get_avatar( $user, 40 ); ?></a>
				</div>
				
				<div class="userpro-sc-i">
					<div class="userpro-sc-i-name"><a href="<?php echo $userpro->permalink( $user ); ?>" title="<?php _e('View Profile','userpro'); ?>"><?php echo userpro_profile_data('display_name', $user); ?></a><?php echo userpro_show_badges( $user ); ?></div>
					<?php if ($userpro->shortbio($user)) : ?><div class="userpro-sc-i-bio"><?php echo $userpro->shortbio( $user ); ?></div><?php endif; ?>
					<div class="userpro-sc-i-icons"><?php echo userpro_profile_icons( $args, $user ); ?>
						<?php if($user_id == get_current_user_id()){?><a href="#" class="userpro-sc-action-remove userpro-tip" style="display: none;" title="<?php _e('Remove Connection','userpro'); ?>" onclick="userpro_remove_connection(<?php echo $user;?>);"><i class="userpro-icon-remove-connection"></i></a><?php }?>
					</div>
				</div>
				
				<div class="userpro-sc-btn">
					<?php // echo $userpro_social->follow_text($user, get_current_user_id()); ?>
					
					<input type=button name="button" id="button-<?php echo $user;?>"  value="<?php _e('Accept','userpro'); ?>" onclick="userpro_accept_request(<?php echo $user; ?>);" class="userpro-button secondary"  >
					<input type=button name="button" id="button-rej-<?php echo $user;?>"  value="<?php _e('Reject','userpro'); ?>" onclick="userpro_reject_request(<?php echo $user; ?>);"  class="userpro-button red" >
				</div>
				
				<div class="userpro-clear"></div>
	
			</div>
		<?php } ?>
		
		<?php }  endforeach; ?>
		
		<?php endif; ?>
		
	
	
	<?php
	// action hook after user header
	if (!isset($user_id)) $user_id = 0;
	$hook_args = array_merge($args, array('user_id' => $user_id));
	//do_action('userpro_after_profile_head', $hook_args);
	?>
	<div class="userpro-body userpro-body-nopad">
		<?php 	$approve_userlist = get_user_meta($user_id,'_userpro_connected_userlist', true); 

		?>
		<?php $j = 0; if (isset($approve_userlist) && $approve_userlist != '' ) : ?>
		
<?php 
	if(isset($args['connected_per_page']))
	{
	$offset=(isset($_GET['connected'])) ? ($_GET['connected']-1)*$args['connected_per_page'] : 0;
	$args['offset'] = $offset;
	$totalconnection  = count($approve_userlist );
	$paginate = paginate_links( array(
						'base'         => add_query_arg('connection' , '%#%'),
						'total'        => ceil($totalconnection/$args['connection_per_page']),
						'current'      => isset($_GET['connection']) ? $_GET['connection'] : 1,
						'show_all'     => false,
						'end_size'     => 1,
						'mid_size'     => 2,
						'prev_next'    => true,
						'prev_text'    => __('Previous','userpro'),
					       'next_text'    => __('Next','userpro'),
						'type'         => 'plain',
						'add_args' => false ,
				));


$j=0;
$arrfollowers=array();
foreach($approve_userlist  as $k => $v)
{
	$j++;
	if($j>$offset && $j<=($offset+$args['connection_per_page'])){
		$arrfollowers[$k] =1 ;
}
}

if($args['connection_paginate']==1)
	$approve_userlist  = $arrfollowers;

}
?>		

		<?php $user_request = array_reverse($approve_userlist, true); foreach($approve_userlist as $user=>$arr) : $userdata = get_userdata($user); if ($userdata) { $j++; ?>
		<div class="userpro-sc" id="<?php echo $user; ?>">
			<div class="userpro-sc-img" data-key="profilepicture">
				<a href="<?php echo $userpro->permalink( $user ); ?>"><?php echo get_avatar( $user, 40 ); ?></a>
			</div>
			
			<div class="userpro-sc-i">
				<div class="userpro-sc-i-name"><a href="<?php echo $userpro->permalink( $user ); ?>" title="<?php _e('View Profile','userpro'); ?>"><?php echo userpro_profile_data('display_name', $user); ?></a><?php echo userpro_show_badges( $user ); ?></div>
				<?php if ($userpro->shortbio($user)) : ?><div class="userpro-sc-i-bio"><?php echo $userpro->shortbio( $user ); ?></div><?php endif; ?>
				<div class="userpro-sc-i-icons"><?php echo userpro_profile_icons( $args, $user ); ?>
					<?php if($user_id == get_current_user_id()){?><a href="#" class="userpro-sc-action-remove userpro-tip" title="<?php _e('Remove Connection','userpro'); ?>" onclick="userpro_remove_connection(<?php echo $user;?>);"><i class="userpro-icon-remove-connection"></i></a><?php }?>
				</div>
			</div>
			
					
			<div class="userpro-sc-btn">
				<?php // echo $userpro_social->follow_text($user, get_current_user_id()); ?>
				
				
			</div>
			
			<div class="userpro-clear"></div>

		</div>
		
		<?php } endforeach; ?>
		
		<?php endif; ?>
		
		<?php if ($j == 0) { // no members ?>
		<div class="userpro-sc userpro-sc-noborder">
			<?php if ($userpro->is_user_logged_user($user_id)) { ?>
			<?php _e('You do not have anyone who started connection with you yet.','userpro'); ?>
			<?php } else { ?>
			<?php _e('This user does not have anyone who started connection him/her yet.','userpro'); ?>
			<?php } ?>
		</div>
		<?php } 
	if(isset($args['connection_paginate']) && $args['connection_paginate']==1)
	{
		?>
		<div class="userpro-paginate bottom"><?php echo $paginate; ?></div>
	<?php }?>
	
	</div>

</div>

