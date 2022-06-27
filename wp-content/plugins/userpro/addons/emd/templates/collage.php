  	<?php $arr = $userpro_emd->collageusers( $args );
	if (isset($arr['users']) && !empty($arr['users']) ) {
	?>
	
	<?php if (isset($arr['paginate']) && $args['emd_paginate'] && $args['emd_paginate_top'] == 1) { ?><div class="userpro-paginate top"><?php echo $arr['paginate']; ?></div><?php } ?>
	<div class="polaroid-images">
	
		<?php
		foreach($arr['users'] as $user) { $user_id = $user->ID; ?>
		
			
		
				<a href="<?php echo $userpro->permalink( $user_id ); ?>">    <img src="<?php echo $userpro->profile_photo_url($user_id); ?>" alt="profile-pic"><?php echo userpro_profile_data('display_name', $user_id); ?><?php do_action('userpro_after_profile_img' , $user_id); ?><?php echo userpro_show_badges( $user_id, true ); ?></a>
	
	
		<?php }} else { ?><div class="userpro-search-noresults"><?php _e('No users match your search. Please try again.','userpro'); ?></div><?php } ?></div>
		<div class="userpro-clear"></div>
<?php if (isset($arr['paginate']) && $args['emd_paginate'] ) { ?><div class="userpro-paginate bottom"><?php echo $arr['paginate']; ?></div><?php } ?>
