<div class="userpro userpro-<?php echo $args['i']; ?> userpro-id-<?php echo $user_id; ?> userpro-<?php echo $args['layout']; ?>" <?php userpro_args_to_data( $args ); ?>>

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
	
	<?php
	// action hook after user header
	if (!isset($user_id)) $user_id = 0;
	$hook_args = array_merge($args, array('user_id' => $user_id));
	do_action('userpro_after_profile_head', $hook_args);
	?>
	
	<div class="userpro-body userpro-body-nopad">
	
		<?php $i = 0; if (isset($following) && $following != '') : ?>
		<?php 
			$offset=(isset($_GET['following'])) ? ($_GET['following']-1)*$args['following_per_page'] : 0;
			$args['offset'] = $offset;
			$totalfollowing = count($following);
			$paginate = paginate_links( array(
					'base'         => add_query_arg('following' , '%#%'),
					'total'        => ceil($totalfollowing/$args['following_per_page']),
					'current'      => isset($_GET['following']) ? $_GET['following'] : 1,
					'show_all'     => false,
					'end_size'     => 1,
					'mid_size'     => 2,
					'prev_next'    => true,
					'prev_text'    => __('« Previous','userpro'),
			       'next_text'    => __('Next »','userpro'),
					'type'         => 'plain',
					'add_args' => false ,
			));
	$i=0;
	$arrfollow=array();
	foreach($following as $k => $v)
	{
		$i++;
		if($i>$offset && $i<=($offset+$args['following_per_page'])){
			$arrfollow[$k] =1 ;

	}

}

if($args['following_paginate']==1)
	$following = $arrfollow;
?>		
	<?php $following = array_reverse($following, true); foreach($following as $user=>$arr) : $userdata = get_userdata($user); if ($userdata) { $i++; ?>
		
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
				<?php echo $userpro_social->follow_text($user); ?>
			</div>
			
			<div class="userpro-clear"></div>

		</div>
		
		<?php } else { /* user not found */ $userpro_social->unset_following($user_id, $user); } endforeach; ?>
		
		<?php endif; ?>
		
		<?php if ($i == 0) { // no members ?>
		<div class="userpro-sc userpro-sc-noborder">
			<?php if ($userpro->is_user_logged_user($user_id)) { ?>
			<?php _e('You have not started following anyone yet.','userpro'); ?>
			<?php } else { ?>
			<?php _e('This user have not started following anyone yet.','userpro'); ?>
			<?php } ?>
		</div>
		<?php } 
			if($args['following_paginate']==1)
			{
		?>
	<div class="userpro-paginate bottom"><?php if(isset($paginate)) echo $paginate; ?></div>
	<?php }?>
	
	</div>

</div>

